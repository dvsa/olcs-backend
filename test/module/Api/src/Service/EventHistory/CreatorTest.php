<?php

namespace Dvsa\OlcsTest\Api\Service\EventHistory;

use Dvsa\Olcs\Api\Domain\Repository\EventHistory as EventHistoryRepo;
use Dvsa\Olcs\Api\Domain\Repository\EventHistoryType as EventHistoryTypeRepo;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\EventHistory\Creator;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CreatorTest
 */
class CreatorTest extends MockeryTestCase
{
    private $authService;

    private $eventHistoryRepo;

    private $eventHistoryTypeRepo;

    private $user;

    public function setUp()
    {
        $this->user = m::mock(UserEntity::class);

        $this->authService = m::mock(AuthorizationService::class);
        $this->authService->shouldReceive('getIdentity->getUser')
            ->withNoArgs()
            ->andReturn($this->user);

        $this->eventHistoryRepo = m::mock(EventHistoryRepo::class);

        $this->eventHistoryTypeRepo = m::mock(EventHistoryTypeRepo::class);

        $this->sut = new Creator(
            $this->authService,
            $this->eventHistoryRepo,
            $this->eventHistoryTypeRepo
        );
    }

    public function testCreateForIrhpApplication()
    {
        $entityId = 100;
        $entityVersion = 1;
        $eventHistoryType = EventHistoryTypeEntity::IRHP_APPLICATION_CREATED;

        $entity = m::mock(IrhpApplicationEntity::class);
        $entity->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($entityId)
            ->shouldReceive('getVersion')
            ->withNoArgs()
            ->andReturn($entityVersion);

        $eventHistoryTypeEntity = m::mock(EventHistoryTypeEntity::class);

        $this->eventHistoryTypeRepo->shouldReceive('fetchOneByEventCode')
            ->with($eventHistoryType)
            ->andReturn($eventHistoryTypeEntity);

        $this->eventHistoryRepo->shouldReceive('save')
            ->with(m::type(EventHistoryEntity::class))
            ->once()
            ->andReturnUsing(
                function (EventHistoryEntity $eventHistory) use ($entity, $entityId, $entityVersion) {
                    $this->assertSame($entity, $eventHistory->getIrhpApplication());
                    $this->assertSame('irhp_application', $eventHistory->getEntityType());
                    $this->assertSame($entityId, $eventHistory->getEntityPk());
                    $this->assertSame($entityVersion, $eventHistory->getEntityVersion());
                }
            );

        $this->sut->create($entity, $eventHistoryType);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot create event history for the entity
     */
    public function testCreateForUndefinedEntity()
    {
        $eventHistoryType = EventHistoryTypeEntity::IRHP_APPLICATION_CREATED;

        $entity = m::mock(EventHistoryEntity::class);

        $eventHistoryTypeEntity = m::mock(EventHistoryTypeEntity::class);

        $this->eventHistoryTypeRepo->shouldReceive('fetchOneByEventCode')
            ->with($eventHistoryType)
            ->andReturn($eventHistoryTypeEntity);

        $this->eventHistoryRepo->shouldReceive('save')
            ->never();

        $this->sut->create($entity, $eventHistoryType);
    }
}
