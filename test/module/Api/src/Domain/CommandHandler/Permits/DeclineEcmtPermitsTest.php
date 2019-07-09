<?php

/**
 * Decline ECMT Permits Application Test
 *
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\DeclineEcmtPermits;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\Permits\DeclineEcmtPermits as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;

/**
 * Class DeclineEcmtPermitsTest
 */
class DeclineEcmtPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeclineEcmtPermits();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_WITHDRAWN,
            WithdrawableInterface::WITHDRAWN_REASON_DECLINED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $applicationId = 1;
        $command = Cmd::create(['id' => $applicationId]);

        $application = m::mock(EcmtPermitApplication::class);
        $application->shouldReceive('decline')->with(
            $this->refData[IrhpInterface::STATUS_WITHDRAWN],
            $this->refData[WithdrawableInterface::WITHDRAWN_REASON_DECLINED]
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
            'messages' => ['ECMT permits declined']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
