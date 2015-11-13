<?php

/**
 * Create Variation Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\CreateVariation;
use Dvsa\Olcs\Transfer\Command\Bus\CreateVariation as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateFeeCmd;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;

/**
 * Create Variation Test
 */
class CreateVariationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateVariation();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            BusRegEntity::STATUS_VAR
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $busRegId = 111;
        $feeId = 44;

        $command = Cmd::create(['id' => $busRegId]);

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('createVariation')
            ->once()
            ->with(RefDataEntity::class, RefDataEntity::class)
            ->andReturnSelf()
            ->shouldReceive('getId')
            ->times(2)
            ->andReturn($busRegId);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $result1 = new Result();
        $result1->addId('fee', $feeId);
        $data = ['id' => $busRegId];
        $this->expectedSideEffect(CreateFeeCmd::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'bus' => $busRegId,
                'fee' => $feeId
            ],
            'messages' => [
                'Variation created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
