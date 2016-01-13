<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ConditionUndertaking;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ConditionUndertaking\DeleteList as CommandHandler;
use Dvsa\Olcs\Transfer\Command\ConditionUndertaking\DeleteList as Command;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Mockery as m;

/**
 * DeleteListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteListTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [73, 324]
        ];
        $command = Command::create($data);

        $mockConditionUndertaking1 = new \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking(
            new \Dvsa\Olcs\Api\Entity\System\RefData(),
            1,
            1
        );
        $mockConditionUndertaking1->setId(73);

        $mockConditionUndertaking2 = new \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking(
            new \Dvsa\Olcs\Api\Entity\System\RefData(),
            0,
            0
        );
        $mockConditionUndertaking2->setId(324);

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchById')->with(73)->once()
            ->andReturn($mockConditionUndertaking1);
        $this->repoMap['ConditionUndertaking']->shouldReceive('delete')->with($mockConditionUndertaking1)->once();

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchById')->with(324)->once()
            ->andReturn($mockConditionUndertaking2);
        $this->repoMap['ConditionUndertaking']->shouldReceive('delete')->with($mockConditionUndertaking2)->once();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['ConditionUndertaking ID 73 deleted', 'ConditionUndertaking ID 324 deleted'],
            $result->getMessages()
        );
    }
}
