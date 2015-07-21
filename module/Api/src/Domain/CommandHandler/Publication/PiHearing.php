<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;

class PiHearing extends AbstractCommandHandler implements TransactionedInterface, PublicationGeneratorAwareInterface
{
    use PublicationGeneratorAwareTrait;

    protected $repoServiceName = 'PublicationLink';

    protected $extraRepos = ['PiHearing', 'Publication'];

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PiHearingEntity $hearing
         * @var PiEntity $pi
         * @var CasesEntity $case
         * @var LicenceEntity $licence
         */
        $hearing = $this->getRepo('PiHearing')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $pi = $hearing->getPi();
        $case = $pi->getCase();

        if ($case->isTm()) {
            return $this->handlePiHearing($hearing, $pi, $case, $command->getText2());
        }

        return $this->handleHearing($hearing, $pi, $case, $command->getText2());
    }

    public function handleHearing(PiHearingEntity $hearing, PiEntity $pi, CasesEntity $case, $text2)
    {
        /** @var LicenceEntity $licence */

        $pubSection = 13;

        $licence = $case->getLicence();
        $licType = $licence->getGoodsOrPsv()->getId();
        $pubType = ($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE ? 'A&D' : 'N&P');
        $trafficArea = $licence->getTrafficArea();
        $publicationSection = $this->getRepo()->getReference(PublicationSectionEntity::class, $pubSection);

        $hearingData = [
            'piVenue' => $hearing->getPiVenue()->getId(),
            'piVenueOther' => $hearing->getPiVenueOther(),
            'hearingDate' => $hearing->getHearingDate(),
            'id' => $hearing->getId()
        ];

        $publication = $this->getRepo('Publication')->fetchLatestForTrafficAreaAndType(
            $trafficArea->getId(),
            $pubType
        );

        //check if we have an existing publication link
        $previousPublication = $this->getRepo()->fetchSingleUnpublished(
            $publication->getId(),
            $pi->getId(),
            $pubSection
        );

        if (!empty($previousPublication)) {
            $publicationLink = $previousPublication;
            $message = 'Publication link updated';
        } else {
            $publicationLink = new PublicationLinkEntity();
            $message = 'Publication link created';
        }

        $publicationLink->updatePiHearing($licence, $pi, $publication, $publicationSection, $trafficArea, $text2);
        $result = $this->createPublication('HearingPublication', $publicationLink, $hearingData);
        $result->addMessage($message);

        return $result;
    }

    public function handleTmHearing(PiHearingEntity $hearing, PiEntity $pi, CasesEntity $case, $text2)
    {
        //$trafficAreas = $hearing->ge

        //return \Doctrine\Common\Util\Debug::dump($publicationLink);
    }

    /**
     * @param $publicationConfig
     * @param PublicationLinkEntity $publicationLink
     * @param $existingContext
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    private function createPublication(
        $publicationConfig,
        PublicationLinkEntity $publicationLink,
        $existingContext
    ) {
        $publicationLink = $this->publicationGenerator->createPublication(
            $publicationConfig,
            $publicationLink,
            $existingContext
        );

        $this->getRepo()->save($publicationLink);

        $result = new Result();
        $result->addId('publicationLink', $publicationLink->getId());

        return $result;
    }
}
