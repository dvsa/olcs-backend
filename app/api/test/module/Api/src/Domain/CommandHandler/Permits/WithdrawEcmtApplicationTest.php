<?php

/**
 * Withdraw ECMT Permit Application Test
 *
 * @author Scott Callaway
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAutomaticallyWithdrawn;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\WithdrawEcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;

/**
 * Class WithdrawEcmtApplicationTest
 */
class WithdrawEcmtApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new WithdrawEcmtPermitApplication();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_WITHDRAWN,
            EcmtPermitApplication::WITHDRAWN_REASON_BY_USER,
            EcmtPermitApplication::WITHDRAWN_REASON_UNPAID,
            EcmtPermitApplication::WITHDRAWN_REASON_DECLINED,
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpReasonProvider
     */
    public function testHandleCommand($withdrawReason, $emailSentTimes)
    {
        $applicationId = 1;
        $feeId1 = 2;
        $feeId2 = 3;

        $cmdData = [
            'id' => $applicationId,
            'reason' => $withdrawReason
        ];
        $command = Cmd::create($cmdData);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('withdraw')->with(
            $this->refData[EcmtPermitApplication::STATUS_WITHDRAWN],
            $this->refData[$withdrawReason]
        )->once();

        $fee1 = m::mock(Fee::class);
        $fee1->shouldReceive('getId')->once()->withNoArgs()->andReturn($feeId1);
        $fee2 = m::mock(Fee::class);
        $fee2->shouldReceive('getId')->once()->withNoArgs()->andReturn($feeId2);
        $fees = [$fee1, $fee2];

        $application->shouldReceive('getOutstandingFees')->once()->withNoArgs()->andReturn($fees);

        $this->expectedSideEffect(
            CancelFee::class,
            [ 'id' => $feeId1],
            new Result()
        );

        $this->expectedSideEffect(
            CancelFee::class,
            [ 'id' => $feeId2],
            new Result()
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtAutomaticallyWithdrawn::class,
            ['id' => $applicationId],
            $applicationId,
            new Result(),
            null,
            $emailSentTimes
        );

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($applicationId)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
              'ecmtPermitApplication' => $applicationId
            ],
            'messages' => ['Permit application withdrawn']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dpReasonProvider()
    {
        return [
            [EcmtPermitApplication::WITHDRAWN_REASON_BY_USER, 0],
            [EcmtPermitApplication::WITHDRAWN_REASON_DECLINED, 0],
            [EcmtPermitApplication::WITHDRAWN_REASON_UNPAID, 1],
        ];
    }
}
