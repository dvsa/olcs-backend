<?php

namespace Dvsa\Olcs\Api\Service\EventHistory;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\EventHistory as EventHistoryRepo;
use Dvsa\Olcs\Api\Domain\Repository\EventHistoryType as EventHistoryTypeRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\User;
use RuntimeException;
use LmcRbacMvc\Service\AuthorizationService;

class Creator
{
    use AuthAwareTrait;

    /**
     * Create service instance
     *
     *
     * @return Creator
     */
    public function __construct(
        AuthorizationService $authService,
        private EventHistoryRepo $eventHistoryRepo,
        private EventHistoryTypeRepo $eventHistoryTypeRepo
    ) {
        $this->authService = $authService;
    }

    /**
     * Create and save event history record
     *
     * @param string $eventHistoryType
     * @return void
     */
    public function create(mixed $entity, $eventHistoryType, $eventData = null)
    {
        // create event history record
        $eventHistory = new EventHistoryEntity(
            $this->getUser(),
            $this->eventHistoryTypeRepo->fetchOneByEventCode($eventHistoryType)
        );

        // link the entity
        switch (true) {
            case $entity instanceof IrhpApplicationEntity:
                $eventHistory->setIrhpApplication($entity);
                $eventHistory->setEntityType('irhp_application');
                break;
            case $entity instanceof User:
                $eventHistory->setUser($entity);
                $eventHistory->setEntityType('user');
                break;
            case $entity instanceof Licence:
                $eventHistory->setLicence($entity);
                $eventHistory->setEntityType('licence');
                break;
            case $entity instanceof Application:
                $eventHistory->setApplication($entity);
                $eventHistory->setEntityType('application');
                break;
            default:
                throw new RuntimeException('Cannot create event history for the entity');
        }

        $eventHistory->setEntityPk($entity->getId());
        $eventHistory->setEntityVersion($entity->getVersion());
        $eventHistory->setEventData($eventData);

        // save
        $this->eventHistoryRepo->save($eventHistory);
    }
}
