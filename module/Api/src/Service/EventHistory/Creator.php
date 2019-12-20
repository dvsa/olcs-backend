<?php

namespace Dvsa\Olcs\Api\Service\EventHistory;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\EventHistory as EventHistoryRepo;
use Dvsa\Olcs\Api\Domain\Repository\EventHistoryType as EventHistoryTypeRepo;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use RuntimeException;
use ZfcRbac\Service\AuthorizationService;

class Creator
{
    use AuthAwareTrait;

    /** @var EventHistoryRepo */
    private $eventHistoryRepo;

    /** @var EventHistoryTypeRepo */
    private $eventHistoryTypeRepo;

    /**
     * Create service instance
     *
     * @param AuthorizationService $authService
     * @param EventHistoryRepo $eventHistoryRepo
     * @param EventHistoryTypeRepo $eventHistoryTypeRepo
     *
     * @return Creator
     */
    public function __construct(
        AuthorizationService $authService,
        EventHistoryRepo $eventHistoryRepo,
        EventHistoryTypeRepo $eventHistoryTypeRepo
    ) {
        $this->authService = $authService;
        $this->eventHistoryRepo = $eventHistoryRepo;
        $this->eventHistoryTypeRepo = $eventHistoryTypeRepo;
    }

    /**
     * Create and save event history record
     *
     * @param mixed $entity
     * @param string $eventHistoryType
     *
     * @return void
     */
    public function create($entity, $eventHistoryType)
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
            default:
                throw new RuntimeException('Cannot create event history for the entity');
        }

        $eventHistory->setEntityPk($entity->getId());
        $eventHistory->setEntityVersion($entity->getVersion());

        // save
        $this->eventHistoryRepo->save($eventHistory);
    }
}
