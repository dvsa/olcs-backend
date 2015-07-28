<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Transfer\Command\Publication\Application as ApplicationCmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedApplicationByApp as UnpublishedApplicationByAppQry;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedApplicationByLic as UnpublishedApplicationByLicQry;

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
         * @var $command ApplicationCmd
         */
        $application = $this->getRepo('Application')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $licence = $application->getLicence();
        $pubType = $application->getGoodsOrPsv()->getId() === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE ?
            'A&D' : 'N&P';
        $appStatus = $application->getStatus()->getId();

        $pubSection = $this->getPublicationSectionId($appStatus);
        $trafficArea = $this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea());
        $publication = $this->getPublication($command->getTrafficArea(), $pubType);
        $publicationSection = $this->getPublicationSection($pubSection);
        /**
         * @var PublicationEntity $publication
         * @var PublicationLinkEntity $publicationLink
         */
        $unpublishedQuery = $this->getUnpublishedApplicationQuery($publication->getId(), $application, $pubSection);
        $publicationLink = $this->getPublicationLink($unpublishedQuery);
        $publicationLink->updateApplication($application, $licence, $publication, $publicationSection, $trafficArea);

        return $this->createPublication('ApplicationPublication', $publicationLink, []);
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
        }

        throw new \Exception('Could not match to a publication section');
    }

    /**
     * @param int $publication
     * @param ApplicationEntity $application
     * @param int $pubSection
     * @return UnpublishedApplicationByLicQry|UnpublishedApplicationByAppQry
     */
    public function getUnpublishedApplicationQuery($publication, $application, $pubSection)
    {
        $checkByLicence = [
            ApplicationEntity::APPLICATION_STATUS_GRANTED,
            ApplicationEntity::APPLICATION_STATUS_REFUSED,
            ApplicationEntity::APPLICATION_STATUS_NOT_TAKEN_UP,
            ApplicationEntity::APPLICATION_STATUS_CURTAILED
        ];

        $data =  [
            'publication' => $publication,
            'publicationSection' => $pubSection
        ];

        //these statuses we check by licence id
        if (in_array($application->getStatus()->getId(), $checkByLicence)) {
            $data['licence'] = $application->getLicence()->getId();
            return UnpublishedApplicationByLicQry::create($data);
        }

        $data['application'] = $application->getId();
        return UnpublishedApplicationByAppQry::create($data);
    }
}
