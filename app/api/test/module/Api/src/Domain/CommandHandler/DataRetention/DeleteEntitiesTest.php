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
        $this->mockRepo('SystemParameter', Repository\SystemParameter::class);
        $this->mockRepo('Queue', Repository\Queue::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['limit' => 9]);

        $this->repoMap['SystemParameter']->shouldReceive('getSystemDataRetentionUser')->with()->once()->andReturn(34);
        $this->repoMap['DataRetention']->shouldReceive('runCleanupProc')->with(9, 34)->once();
        $this->repoMap['Queue']->shouldReceive('isItemTypeQueued')->with(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)->once()
            ->andReturn(true);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }


    public function testHandleCommandQueueDeleteDocuments()
    {
        $command = Cmd::create(['limit' => 9]);

        $this->repoMap['SystemParameter']->shouldReceive('getSystemDataRetentionUser')->with()->once()->andReturn(34);
        $this->repoMap['DataRetention']->shouldReceive('runCleanupProc')->with(9, 34)->once();
        $this->repoMap['Queue']->shouldReceive('isItemTypeQueued')->with(Queue::TYPE_REMOVE_DELETED_DOCUMENTS)->once()
            ->andReturn(false);

        $this->expectedSideEffect(
            Create::class,
            ['type' => Queue::TYPE_REMOVE_DELETED_DOCUMENTS, 'status' => Queue::STATUS_QUEUED],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
