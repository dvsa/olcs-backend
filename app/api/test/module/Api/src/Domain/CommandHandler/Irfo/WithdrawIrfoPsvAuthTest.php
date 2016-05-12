<?php

/**
 * Withdraw Irfo Psv Auth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelIrfoPsvAuthFees as CancelFeesDto;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\WithdrawIrfoPsvAuth as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Transfer\Command\Irfo\WithdrawIrfoPsvAuth as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Withdraw Irfo Psv Auth Test
 */
class WithdrawIrfoPsvAuthTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            IrfoPsvAuthEntity::STATUS_WITHDRAWN,
        ];

        $this->references = [
            IrfoPsvAuthType::class => [
                22 => m::mock(IrfoPsvAuthType::class)
            ],
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;

        $data = [
            'id' => $id,
            'version' => 2,
            'organisation' => 11,
            'irfoPsvAuthType' => 22,
            'status' => IrfoPsvAuthEntity::STATUS_PENDING,
            'validityPeriod' => 1,
            'inForceDate' => '2015-01-01',
            'expiryDate' => '2016-01-01',
            'applicationSentDate' => '2014-01-01',
            'serviceRouteFrom' => 'From',
            'serviceRouteTo' => 'To',
            'journeyFrequency' => IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            'isFeeExemptApplication' => 'Y',
            'isFeeExemptAnnual' => 'Y',
            'exemptionDetails' => 'testing',
            'copiesRequired' => 1,
            'copiesRequiredTotal' => 1,
            'countrys' => ['GB'],
            'irfoPsvAuthNumbers' => [],
        ];

        $command = Cmd::create($data);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth->setIrfoPsvAuthNumbers([]);
        $irfoPsvAuth
            ->shouldReceive('update')
            ->once()
            ->shouldReceive('withdraw')
            ->once()
            ->with($this->refData[IrfoPsvAuthEntity::STATUS_WITHDRAWN])
            ->shouldReceive('getId')
            ->andReturn($id);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save')
            ->with(m::type(IrfoPsvAuthEntity::class))
            ->once();

        $this->expectedSideEffect(CancelFeesDto::class, ['id' => $id], new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
