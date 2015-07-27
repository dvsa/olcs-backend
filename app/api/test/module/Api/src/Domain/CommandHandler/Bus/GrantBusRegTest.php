<?php

/**
 * Grant BusReg Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\GrantBusReg;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Transfer\Command\Bus\GrantBusReg as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Grant BusReg Test
 */
class GrantBusRegTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GrantBusReg();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            BusRegEntity::STATUS_REGISTERED,
            'brvr_route'
        ];

        parent::initReferences();
    }

    /**
     * Tests grant throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleCommandThrowsIncorrectStatusException()
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'id' => $id,
                'variationReasons' => ['brvr_route']
            ]
        );

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getStatusForGrant')
            ->andReturn(null);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg);

        $this->sut->handleCommand($command);
    }

    /**
     * Tests grant throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandThrowsMissingVariationReasonsException()
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'id' => $id,
            ]
        );

        $status = new RefDataEntity();
        $status->setId(BusRegEntity::STATUS_VAR);

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getStatusForGrant')
            ->andReturn(BusRegEntity::STATUS_REGISTERED)
            ->shouldReceive('getStatus')
            ->andReturn($status);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg);

        $this->sut->handleCommand($command);
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
                'variationReasons' => ['brvr_route']
            ]
        );

        $status = new RefDataEntity();
        $status->setId(BusRegEntity::STATUS_VAR);

        /** @var BusEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getStatusForGrant')
            ->andReturn(BusRegEntity::STATUS_REGISTERED)
            ->shouldReceive('getStatus')
            ->andReturn($status)
            ->shouldReceive('grant')
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
