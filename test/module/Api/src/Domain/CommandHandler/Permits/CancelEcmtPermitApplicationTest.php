<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

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
        $commandId = 4096;
        $applicationId = 4096;

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
            ->andReturn($commandId);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($commandId)
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
