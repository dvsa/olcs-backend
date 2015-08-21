<?php

/**
 * Delete Bus Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\DeleteBus;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Bus\DeleteBus as Cmd;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Delete Bus Test
 */
class DeleteBusTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteBus();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;
        $previousId = 98;

        $routeNo = 11;
        $variationNo = 22;

        $command = Cmd::Create(['id' => $id,]);

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusEntity::class);
        $busReg->shouldReceive('getRouteNo')
            ->once()
            ->andReturn($routeNo)
            ->shouldReceive('getVariationNo')
            ->once()
            ->andReturn($variationNo)
            ->shouldReceive('canDelete')
            ->once()
            ->andReturn(true);

        $mockPreviousBusReg = m::mock(BusEntity::class);
        $mockPreviousBusReg->shouldReceive('getId')-> andReturn($previousId);

        $mockFetchList = m::mock(\ArrayIterator::class);
        $mockFetchList->shouldReceive('count')->andReturn(1);
        $mockFetchList->shouldReceive('current')->andReturn($mockPreviousBusReg);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('fetchList')
            ->once()
            ->andReturn($mockFetchList)
            ->shouldReceive('delete')
            ->with(m::type(BusEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($previousId, $result->getId('previousBusRegId'));
    }
}
