<?php

/**
 * Grant BusReg Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\GrantBusReg;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Transfer\Command\Bus\GrantBusReg as Cmd;
use Dvsa\Olcs\Transfer\Command\Publication\Bus as PublishDto;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrCancelled;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRegistered;

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
            BusRegEntity::STATUS_CANCELLED,
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

        /** @var BusRegEntity $busReg */
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

        /** @var BusRegEntity $busReg */
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
     * @dataProvider handleCommandProvider
     *
     * @param string $oldStatus
     */
    public function testHandleCommand($oldStatus)
    {
        $id = 99;

        $command = Cmd::Create(
            [
                'id' => $id,
                'variationReasons' => ['brvr_route']
            ]
        );

        $status = new RefDataEntity();
        $status->setId($oldStatus);

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class)->makePartial();
        $busReg->shouldReceive('canMakeDecision')->once()->andReturn(true);
        $busReg->shouldReceive('isGrantable')->once()->andReturn(true);
        $busReg->shouldReceive('getEbsrSubmissions')->andReturn(new ArrayCollection());
        $busReg->setId($id);
        $busReg->setStatus($status);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $this->expectedSideEffect(
            PublishDto::class,
            ['id' => $id],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * data provider for testHandleCommand
     *
     * @return array
     */
    public function handleCommandProvider()
    {
        return [
            [BusRegEntity::STATUS_VAR],
            [BusRegEntity::STATUS_CANCEL]
        ];
    }

    /**
     * @dataProvider handleCommandEbsrProvider
     *
     * @param string $oldStatus
     * @param string $emailSideEffectClass
     */
    public function testHandleCommandEbsrRecord($oldStatus, $emailSideEffectClass)
    {
        $id = 99;
        $ebsrId = 55;

        $command = Cmd::Create(
            [
                'id' => $id,
                'variationReasons' => ['brvr_route']
            ]
        );

        $status = new RefDataEntity();
        $status->setId($oldStatus);

        $ebsrSubmission = m::mock(EbsrSubmissionEntity::class);
        $ebsrSubmission->shouldReceive('getId')->andReturn($ebsrId);
        $ebsrSubmissions = new ArrayCollection([$ebsrSubmission]);

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class)->makePartial();
        $busReg->shouldReceive('canMakeDecision')->once()->andReturn(true);
        $busReg->shouldReceive('isGrantable')->once()->andReturn(true);
        $busReg->shouldReceive('getEbsrSubmissions')->andReturn($ebsrSubmissions);
        $busReg->shouldReceive('isFromEbsr')->andReturn(true);
        $busReg->setId($id);
        $busReg->setStatus($status);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $this->expectedSideEffect(
            PublishDto::class,
            ['id' => $id],
            new Result()
        );

        $this->expectedEmailQueueSideEffect(
            $emailSideEffectClass,
            ['id' => $ebsrId],
            $ebsrId,
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * Data provider for testHandleCommandEbsrRecord
     *
     * @return array
     */
    public function handleCommandEbsrProvider()
    {
        return [
            [BusRegEntity::STATUS_VAR, SendEbsrRegistered::class],
            [BusRegEntity::STATUS_CANCEL, SendEbsrCancelled::class]
        ];
    }
}
