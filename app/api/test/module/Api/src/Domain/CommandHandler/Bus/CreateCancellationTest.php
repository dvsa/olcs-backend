<?php

/**
 * Create Cancellation Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\CreateCancellation;
use Dvsa\Olcs\Transfer\Command\Bus\CreateCancellation as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Cancellation Test
 */
class CreateCancellationTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateCancellation();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            BusRegEntity::STATUS_CANCEL
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 111;

        $command = Cmd::create(['id' => $id]);

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('createVariation')
            ->once()
            ->with(RefDataEntity::class, RefDataEntity::class)
            ->andReturnSelf()
            ->shouldReceive('getId')
            ->once()
            ->andReturn($id);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'bus' => 111,
            ],
            'messages' => [
                'Cancellation created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
