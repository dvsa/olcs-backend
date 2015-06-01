<?php

/**
 * CancelAllInterimFeesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CancelAllInterimFees;

/**
 * CancelAllInterimFeesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CancelAllInterimFeesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CancelAllInterimFees();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('Fee', \Dvsa\Olcs\Api\Domain\Repository\Fee::class);

        parent::setUp();
    }

    public function testHandleCommandWithException()
    {
        for ($id = 23; $id < 26; $id++) {
            $mockFee = m::mock(FeeEntity::class)->makePartial();
            $mockFee->setId($id);
        }

        $this->repoMap['Application']->shouldReceive('beginTransaction')->with()->once();

        $this->repoMap['Fee']->shouldReceive('fetchInterimFeesByApplicationId')->with(542, true)->once()
            ->andThrow('\Exception');

        $this->repoMap['Application']->shouldReceive('rollback')->with()->once();

        $this->setExpectedException('\Exception');

        $command = \Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee::create(['id' => 542]);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        for ($id = 23; $id < 26; $id++) {
            $mockFee = m::mock(FeeEntity::class)->makePartial();
            $mockFee->setId($id);
            $fees[] = $mockFee;
        }

        $this->repoMap['Application']->shouldReceive('beginTransaction')->with()->once();

        $this->repoMap['Fee']->shouldReceive('fetchInterimFeesByApplicationId')->with(542, true)->once()
            ->andReturn($fees);

        $this->expectedSideEffect(\Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee::class, ['id' => 23], new Result());
        $this->expectedSideEffect(\Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee::class, ['id' => 24], new Result());
        $this->expectedSideEffect(\Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee::class, ['id' => 25], new Result());

        $this->repoMap['Application']->shouldReceive('commit')->with()->once();

        $command = \Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee::create(['id' => 542]);
        $result = $this->sut->handleCommand($command);

        $this->assertContains('CancelAllInterimFees success', $result->getMessages());
    }
}
