<?php

/**
 * Refuse Bus Reg By Short Notice Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\RefuseBusRegByShortNotice;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Bus\RefuseBusRegByShortNotice as Cmd;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Refuse Bus Reg By Short Notice Test
 */
class RefuseBusRegByShortNoticeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RefuseBusRegByShortNotice();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
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
            ]
        );

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('refuseByShortNotice')
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
