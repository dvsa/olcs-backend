<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Publication\Impounding as ImpoundingPublicationCmd;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Cases\Impounding as ImpoundingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedPi as UnpublishedPiQry;

/**
 * Impounding publication command handler
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Publication
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Impounding extends AbstractCommandHandler implements TransactionedInterface, PublicationGeneratorAwareInterface
{
    use PublicationGeneratorAwareTrait;
    use CreatePublicationTrait;

    protected $repoServiceName = 'PublicationLink';

    protected $extraRepos = ['Impounding', 'Publication', 'TrafficArea'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var ImpoundingEntity $impounding
         * @var CasesEntity $case
         * @var $command ImpoundingPublicationCmd
         */
        $impounding = $this->getRepo('Impounding')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $case = $impounding->getCase();

        $caseType = $case->getCaseType()->getId();
        if ($caseType === CasesEntity::APP_CASE_TYPE) {
            return $this->handleApplicationCaseImpounding($command, $impounding, $case);
        }

        return $this->handleLicenceCaseImpounding($command, $impounding, $case);
    }

    public function handleApplicationCaseImpounding(
        CommandInterface $command,
        ImpoundingEntity $impounding,
        CasesEntity $case
    ) {
        $result = new Result();

        /**
         * @var PublicationSectionEntity $publicationSection
         * @var TrafficAreaEntity $trafficArea
         * @var ImpoundingPublicationCmd $command
         */
        $pubSection = PublicationSectionEntity::HEARING_SECTION;
        $handler = 'ImpoundingPublication';

        $publicationSection = $this->getPublicationSection(PublicationSectionEntity::HEARING_SECTION);
        $trafficArea = $this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea());
        $pubType = $command->getPubType();

        //default Northern Ireland N&P to already published (as it doesn't exist)
        $publishedAreas = [
            TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE => [
                'N&P' => true
            ]
        ];

        //process the traffic areas where we're adding or amending the publication

        //no N&P for Northern Ireland, this is already defaulted to published, no need to record it twice below
        if (
            ($trafficArea->getId() !== TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) &&
            ($pubType !== 'N&P')
        ) {

            //record that we've dealt with this traffic area and pub type combination
            $publishedAreas[$trafficArea->getId()][$pubType] = true;

            /**
             * @var UnpublishedPiQry $unpublishedQuery
             * @var PublicationEntity $publication
             * @var PublicationLinkEntity $publicationLink
             */
            $publication = $this->getPublication($trafficArea->getId(), $pubType);
            $unpublishedQuery = $this->getUnpublishedImpoundingQuery(
                $publication->getId(),
                $impounding->getId(),
                $pubSection
            );
            $publicationLink = $this->getPublicationLink($unpublishedQuery);

            if ($publicationLink->getId() === null) {
                $publicationLink->createImpounding(
                    $transportManager,
                    $pi,
                    $publication,
                    $publicationSection,
                    $trafficArea
                );
            }

                $publicationLink->setText2($command->getText2());

                $result->merge(
                    $this->createPublication(
                        $handler,
                        $publicationLink,
                        $this->extractHearingData($hearing)
                    )
                );
            }
        }

        //if we haven't published to a traffic area, check whether there's an existing publication we need to delete
        foreach ($allTrafficAreas as $trafficArea) {
            foreach ($allPubTypes as $pubType) {
                if (isset($publishedAreas[$trafficArea->getId()][$pubType])) {
                    continue;
                }

                //check for a previous publication
                $publication = $this->getPublication($trafficArea->getId(), $pubType);
                $unpublishedQuery = $this->getUnpublishedPiQuery($publication->getId(), $pi->getId(), $pubSection);
                $publicationLink = $this->getPublicationLink($unpublishedQuery);

                //if previous publication is found, remove it
                if ($publicationLink->getId() !== null) {
                    $this->clearPoliceData($publicationLink);
                    $this->getRepo()->delete($publicationLink);
                }
            }
        }

        return $result;
    }

    /**
     * @param int $publication
     * @param int $pi
     * @param int $pubSection
     * @return UnpublishedPiQry
     */
    private function getUnpublishedImpoundingQuery($publication, $pi, $pubSection)
    {
        $data =  [
            'publication' => $publication,
            'impounding' => $impounding,
            'publicationSection' => $pubSection
        ];

        return UnpublishedPiQry::create($data);
    }

    /**
     * @param PiHearingEntity $hearing
     * @param PiEntity $pi
     * @param CasesEntity $case
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleHearing(PiHearingEntity $hearing, PiEntity $pi, CasesEntity $case, CommandInterface $command)
    {
        /**
         * @var LicenceEntity $licence
         * @var PiHearingCmd|PiDecisionCmd $command
         */
        if ($command instanceof PiHearingCmd) {
            $pubSection = PublicationSectionEntity::HEARING_SECTION;
            $handler = 'HearingPublication';
        } else {
            $pubSection = PublicationSectionEntity::DECISION_SECTION;
            $handler = 'HearingDecision';
        }

        $caseType = $case->getCaseType()->getId();
        $licence = $case->getLicence();

        if ($caseType === CasesEntity::APP_CASE_TYPE) {
            $licType = $case->getApplication()->getGoodsOrPsv()->getId();
        } else {
            $licType = $licence->getGoodsOrPsv()->getId();
        }

        $pubType = ($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE ? 'A&D' : 'N&P');
        $trafficArea = $licence->getTrafficArea();
        $publicationSection = $this->getPublicationSection($pubSection);

        /**
         * @var UnpublishedPiQry $unpublishedQuery
         * @var PublicationEntity $publication
         * @var PublicationLinkEntity $publicationLink
         */
        $publication = $this->getPublication($trafficArea->getId(), $pubType);
        $unpublishedQuery = $this->getUnpublishedPiQuery($publication->getId(), $pi->getId(), $pubSection);
        $publicationLink = $this->getPublicationLink($unpublishedQuery);

        if ($publicationLink->getId() === null) {
            $publicationLink->createPiHearing($licence, $pi, $publication, $publicationSection, $trafficArea);
        }

        $publicationLink->setText2($command->getText2());

        return $this->createPublication($handler, $publicationLink, $this->extractHearingData($hearing));
    }

    /**
     * @param int $publication
     * @param int $pi
     * @param int $pubSection
     * @return UnpublishedPiQry
     */
    private function getUnpublishedPiQuery($publication, $pi, $pubSection)
    {
        $data =  [
            'publication' => $publication,
            'pi' => $pi,
            'publicationSection' => $pubSection
        ];

        return UnpublishedPiQry::create($data);
    }

    /**
     * @param PiHearingEntity $hearing
     * @return array
     */
    private function extractHearingData($hearing)
    {
        $piVenue = $hearing->getPiVenue();
        $hearingDate = $hearing->getHearingDate();

        //sometimes we have a datetime, and sometimes a string
        if ($hearingDate instanceof \DateTime) {
            $hearingDate = $hearingDate->format('Y-m-d H:i:s');
        }

        return [
            'piVenue' => ($piVenue === null ? $piVenue : $piVenue->getId()),
            'piVenueOther' => $hearing->getPiVenueOther(),
            'hearingDate' => $hearingDate,
            'id' => $hearing->getId()
        ];
    }
}
