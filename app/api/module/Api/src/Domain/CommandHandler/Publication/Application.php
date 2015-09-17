<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Transfer\Command\Publication\Application as ApplicationCmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedApplication as UnpublishedApplicationQry;

/**
 * Application command handler
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Publication
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Application extends AbstractCommandHandler implements TransactionedInterface, PublicationGeneratorAwareInterface
{
    use PublicationGeneratorAwareTrait;
    use CreatePublicationTrait;

    protected $repoServiceName = 'PublicationLink';

    protected $extraRepos = ['Application', 'Publication', 'TrafficArea'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var LicenceEntity $licence
         * @var ApplicationEntity $application
         * @var PublicationSectionEntity $publicationSection
         * @var PublicationEntity $publication
         * @var PublicationLinkEntity $publicationLink
         * @var $command ApplicationCmd
         */
        $application = $this->getRepo('Application')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $licence = $application->getLicence();
        $pubType = $application->getGoodsOrPsv()->getId() === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE ?
            'A&D' : 'N&P';
        $appStatus = $application->getStatus()->getId();

        $pubSection = $command->getPublicationSection();
        if (empty($pubSection)) {
            $pubSection = $this->getPublicationSectionId($appStatus);
        }
        $trafficArea = $this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea());

        $publication = $this->getPublication($command->getTrafficArea(), $pubType);

        $publicationSection = $this->getPublicationSection($pubSection);

        $unpublishedQuery = $this->getUnpublishedApplicationQuery(
            $publication->getId(),
            $application->getId(),
            $pubSection
        );

        $publicationLink = $this->getPublicationLink($unpublishedQuery);

        if ($publicationLink->getId() === null) {
            $publicationLink->createApplication(
                $application,
                $licence,
                $publication,
                $publicationSection,
                $trafficArea
            );
        }

        // switch configuration if its a variation
        $publicationConfig = $application->isNew() ? 'ApplicationPublication' : 'VariationPublication';

        return $this->createPublication($publicationConfig, $publicationLink, []);
    }

    public function getPublicationSectionId($appStatus)
    {
        switch ($appStatus) {
            case ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION:
                return PublicationSectionEntity::APP_NEW_SECTION;
            case ApplicationEntity::APPLICATION_STATUS_GRANTED:
                return PublicationSectionEntity::APP_GRANTED_SECTION;
            case ApplicationEntity::APPLICATION_STATUS_REFUSED:
                return PublicationSectionEntity::APP_REFUSED_SECTION;
            case ApplicationEntity::APPLICATION_STATUS_WITHDRAWN:
                return PublicationSectionEntity::APP_WITHDRAWN_SECTION;
            case ApplicationEntity::APPLICATION_STATUS_NOT_TAKEN_UP:
                return PublicationSectionEntity::APP_GRANT_NOT_TAKEN_SECTION;
        }

        throw new ForbiddenException('Could not match to a publication section');
    }

    /**
     * @param int $publication
     * @param int $application
     * @param int $pubSection
     * @return UnpublishedApplicationQry
     */
    public function getUnpublishedApplicationQuery($publication, $application, $pubSection)
    {
        $data =  [
            'publication' => $publication,
            'application' => $application,
            'publicationSection' => $pubSection
        ];

        return UnpublishedApplicationQry::create($data);
    }
}
