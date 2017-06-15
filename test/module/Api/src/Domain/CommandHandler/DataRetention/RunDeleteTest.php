<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention\RunDelete;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\DataRetention\RunDelete as Cmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class RunDeleteTest
 */
class RunDeleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RunDelete();
        $this->mockRepo('DataRetentionRule', DataRetentionRule::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([]);

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
