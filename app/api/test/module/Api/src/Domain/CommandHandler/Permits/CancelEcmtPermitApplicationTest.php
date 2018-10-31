<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CancelEcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class CancelEcmtPermitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new CancelEcmtPermitApplication();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_CANCELLED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $applicationId = 4096;
        $feeId1 = 111;
        $feeId2 = 222;

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('cancel')
            ->with($this->mapRefData(EcmtPermitApplication::STATUS_CANCELLED))
            ->once()
            ->globally()
            ->ordered();

        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($applicationId);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($applicationId);

        $fee1 = m::mock(Fee::class);
        $fee1->shouldReceive('getId')->once()->withNoArgs()->andReturn($feeId1);
        $fee2 = m::mock(Fee::class);
        $fee2->shouldReceive('getId')->once()->withNoArgs()->andReturn($feeId2);
        $fees = [$fee1, $fee2];

        $ecmtPermitApplication->shouldReceive('getOutstandingFees')->once()->withNoArgs()->andReturn($fees);

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

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($applicationId)
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($applicationId, $result->getId('ecmtPermitApplication'));
        $this->assertEquals(['Permit application cancelled'], $result->getMessages());
    }
}
