<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention\DeleteEntities;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\DataRetention;
use Dvsa\Olcs\Api\Entity\DataRetentionRule;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\DataRetention\DeleteEntities as Cmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class DeleteEntitiesTest
 */
class DeleteEntitiesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteEntities();
        $this->mockRepo('DataRetention', Repository\DataRetention::class);
        $this->mockRepo('DataRetentionRule', Repository\DataRetentionRule::class);
        $this->mockRepo('Queue', Repository\Queue::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);
        /** @var User $currentUser */
        $currentUser = m::mock(User::class)->makePartial();
        $currentUser->setId(222);
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleCommandIterateEntities()
    {
        $command = Cmd::create([]);

        $dataRetentionRule = (new DataRetentionRule())
            ->setActionProcedure('ACTION_PROC');
        $dataRetention1 = (new DataRetention())
            ->setId(11)
            ->setEntityPk(123)
            ->setDataRetentionRule($dataRetentionRule);
        $dataRetention2 = (new DataRetention())
            ->setId(12)
            ->setEntityPk(999)
            ->setDataRetentionRule($dataRetentionRule);

        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(10)->once()
            ->andReturn([$dataRetention1, $dataRetention2]);
        $this->repoMap['DataRetentionRule']->shouldReceive('runActionProc')->with('ACTION_PROC', 123, 222)->once()
            ->andReturn(false);
        $this->repoMap['DataRetentionRule']->shouldReceive('runActionProc')->with('ACTION_PROC', 999, 222)->once()
            ->andReturn(true);
        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(1)->once()->andReturn([]);
        $this->repoMap['Queue']->shouldReceive('isItemTypeQueued')->with(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)->once()
            ->andReturn(true);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ERROR data_retention.id = 11, ACTION_PROC',
                'SUCCESS data_retention.id = 12, ACTION_PROC',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandCreateRemoveDocsJob()
    {
        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(10)->once()
            ->andReturn([]);
        $this->repoMap['Queue']->shouldReceive('isItemTypeQueued')->with(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)->once()
            ->andReturn(false);
        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(1)->once()->andReturn([]);

        $this->expectedSideEffect(
            Create::class,
            ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED],
            new Result()
        );

        $command = Cmd::create([]);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandCreateAnotherProcessJob()
    {
        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(10)->once()
            ->andReturn([]);
        $this->repoMap['Queue']->shouldReceive('isItemTypeQueued')->with(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)->once()
            ->andReturn(true);
        $this->repoMap['DataRetention']->shouldReceive('fetchEntitiesToDelete')->with(1)->once()->andReturn(['FOO']);

        $this->expectedSideEffect(
            Create::class,
            ['type' => Queue::TYPE_PROCESS_DATA_RETENTION, 'status' => Queue::STATUS_QUEUED],
            new Result()
        );

        $command = Cmd::create([]);
        $this->sut->handleCommand($command);
    }
}
