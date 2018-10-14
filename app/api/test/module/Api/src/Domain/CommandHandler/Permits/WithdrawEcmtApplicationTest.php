<?php

/**
 * Withdraw ECMT Permit Application Test
 *
 * @author Scott Callaway
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

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
            EcmtPermitApplication::WITHDRAWN_REASON_BY_USER
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $applicationId = 1;
        $cmdData = [
            'id' => $applicationId,
            'reason' => EcmtPermitApplication::WITHDRAWN_REASON_BY_USER
        ];
        $command = Cmd::create($cmdData);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('withdraw')->with(
            $this->refData[EcmtPermitApplication::STATUS_WITHDRAWN],
            $this->refData[EcmtPermitApplication::WITHDRAWN_REASON_BY_USER]
        )->once();

        $fee_1 = m::mock(Fee::class);
        $fee_1->shouldReceive('getId')->andReturn(1);
        $fees = [$fee_1];
        $application->shouldReceive('getOutstandingFees')->andReturn($fees);

        $taskResult = new Result();
        $this->expectedSideEffect(
            CancelFee::class,
            [ 'id' => 1],
            $taskResult
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
}
