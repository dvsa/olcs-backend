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
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'id' => $id,
                'reason' => 'reg_in_error'
            ]
        );

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('withdraw')
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id);

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
