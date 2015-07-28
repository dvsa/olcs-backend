<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Transfer\Command\Publication\PiHearing as PiHearingCmd;
use Dvsa\Olcs\Transfer\Command\Publication\PiDecision as PiDecisionCmd;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedPi as UnpublishedPiQry;

/**
 * Pi Hearing command handler
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Publication
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiHearing extends AbstractCommandHandler implements TransactionedInterface, PublicationGeneratorAwareInterface
{
    use PublicationGeneratorAwareTrait;
    use CreatePublicationTrait;

    protected $repoServiceName = 'PublicationLink';

    protected $extraRepos = ['PiHearing', 'Publication', 'TrafficArea'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PiHearingEntity $hearing
         * @var PiEntity $pi
         * @var CasesEntity $case
         * @var LicenceEntity $licence
         * @var $command PiHearingCmd|PiDecisionCmd
         */
        $hearing = $this->getRepo('PiHearing')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $pi = $hearing->getPi();
        $case = $pi->getCase();

        if ($case->isTm()) {
            return $this->handleTmHearing($command, $hearing, $pi, $case);
        }

        return $this->handleHearing($hearing, $pi, $case, $command);
    }

    public function handleTmHearing(
        CommandInterface $command,
        PiHearingEntity $hearing,
        PiEntity $pi,
        CasesEntity $case
    ) {
        $result = new Result();

        /**
         * @var TransportManagerEntity $transportManager
         * @var PublicationSectionEntity $publicationSection
         * @var TrafficAreaEntity $trafficArea
         * @var PiHearingCmd|PiDecisionCmd $command
         */
        if ($command instanceof PiHearingCmd) {
            $pubSection = PublicationSectionEntity::TM_HEARING_SECTION;
            $handler = 'TmHearingPublication';
        } else {
            $pubSection = PublicationSectionEntity::TM_DECISION_SECTION;
            $handler = 'TmHearingDecision';
        }

        $transportManager = $case->getTransportManager();
        $publicationSection = $this->getPublicationSection($pubSection);
        $trafficAreas = $command->getTrafficAreas();
        $pubTypes = $command->getPubTypes();

        if (in_array('all', $trafficAreas)) {
            $trafficAreas = $this->getRepo('TrafficArea')->fetchAll();
        }

        if (in_array('All', $pubTypes)) {
            $pubTypes = ['A&D', 'N&P'];
        }

        foreach ($trafficAreas as $ta) {
            foreach ($pubTypes as $pubType) {
                if ($ta instanceof TrafficAreaEntity) {
                    $trafficArea = $this->getRepo()->getReference(TrafficAreaEntity::class, $ta->getId());
                } else {
                    $trafficArea = $this->getRepo()->getReference(TrafficAreaEntity::class, $ta);
                }

                //no N&P for Northern Ireland
                if ($trafficArea->getId() === TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
                    && $pubType == 'N&P') {
                    continue;
                }

                /**
                 * @var UnpublishedPiQry $unpublishedQuery
                 * @var PublicationEntity $publication
                 * @var PublicationLinkEntity $publicationLink
                 */
                $publication = $this->getPublication($trafficArea->getId(), $pubType);
                $unpublishedQuery = $this->getUnpublishedPiQuery($publication->getId(), $pi->getId(), $pubSection);
                $publicationLink = $this->getPublicationLink($unpublishedQuery);
                $publicationLink->updateTmPiHearing(
                    $transportManager,
                    $pi,
                    $publication,
                    $publicationSection,
                    $trafficArea,
                    $command->getText2()
                );

                $result->merge(
                    $this->createPublication(
                        $handler,
                        $publicationLink,
                        $this->extractHearingData($hearing)
                    )
                );
            }
        }

        return $result;
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

        $licence = $case->getLicence();
        $licType = $licence->getGoodsOrPsv()->getId();
        $pubType = ($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE ? 'A&D' : 'N&P');
        $trafficArea = $licence->getTrafficArea();
        $publicationSection = $this->getPublicationSection($pubSection);
        $text2 = $command->getText2();

        /**
         * @var UnpublishedPiQry $unpublishedQuery
         * @var PublicationEntity $publication
         * @var PublicationLinkEntity $publicationLink
         */
        $publication = $this->getPublication($trafficArea->getId(), $pubType);
        $unpublishedQuery = $this->getUnpublishedPiQuery($publication->getId(), $pi->getId(), $pubSection);
        $publicationLink = $this->getPublicationLink($unpublishedQuery);
        $publicationLink->updatePiHearing($licence, $pi, $publication, $publicationSection, $trafficArea, $text2);

        return $this->createPublication($handler, $publicationLink, $this->extractHearingData($hearing));
    }

    /**
     * @param int $publication
     * @param int $pi
     * @param int $pubSection
     * @return UnpublishedPiQry
     */
    public function getUnpublishedPiQuery($publication, $pi, $pubSection)
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
    public function extractHearingData($hearing)
    {
        return [
            'piVenue' => $hearing->getPiVenue()->getId(),
            'piVenueOther' => $hearing->getPiVenueOther(),
            'hearingDate' => $hearing->getHearingDate(),
            'id' => $hearing->getId()
        ];
    }
}
