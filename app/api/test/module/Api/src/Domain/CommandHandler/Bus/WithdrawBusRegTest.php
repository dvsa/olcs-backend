<?php

/**
 * Withdraw BusReg Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\WithdrawBusReg;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Bus\WithdrawBusReg as Cmd;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrWithdrawn;

/**
 * Withdraw BusReg Test
 */
class WithdrawBusRegTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new WithdrawBusReg();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            BusRegEntity::STATUS_WITHDRAWN,
            'reg_in_error'
        ];

        parent::initReferences();
    }

    /**
     * test handleCommand when Ebsr
     */
    public function testHandleCommandEbsr()
    {
        $id = 99;
        $ebsrId = 55;

        $command = Cmd::Create(
            [
                'id' => $id,
                'reason' => 'reg_in_error'
            ]
        );

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('withdraw')->once();
        $busReg->shouldReceive('getId')->andReturn($id);
        $busReg->shouldReceive('isFromEbsr')->once()->andReturn(true);
        $busReg->shouldReceive('getEbsrSubmissions->first->getId')->once()->andReturn($ebsrId);
        $this->expectedSideEffect(SendEbsrWithdrawn::class, ['id' => $ebsrId], new Result());

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * test handleCommand when not Ebsr
     */
    public function testHandleCommandNotEbsr()
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'id' => $id,
                'reason' => 'reg_in_error'
            ]
        );

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('withdraw')->once();
        $busReg->shouldReceive('getId')->andReturn($id);
        $busReg->shouldReceive('isFromEbsr')->once()->andReturn(false);
        $busReg->shouldReceive('getEbsrSubmissions->first->getId')->never();

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
