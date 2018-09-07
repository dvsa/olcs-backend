<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtTrips;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class UpdateEcmtTripsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new UpdateEcmtTrips();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $commandId = 3;
        $ecmtTrips = 7;
        $ecmtPermitApplicationId = 10;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($commandId);
        $command->shouldReceive('getEcmtTrips')
            ->andReturn($ecmtTrips);
   
        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $ecmtPermitApplication->shouldReceive('updateTrips')
            ->with($ecmtTrips)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($commandId)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'Permit application updated'
            ],
            $result->getMessages()
        );

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );
    }
}
