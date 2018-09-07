<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtCabotage;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class UpdateEcmtCabotageTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new UpdateEcmtCabotage();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cabotage = true;
        $ecmtPermitApplicationId = 15;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getCabotage')
            ->andReturn($cabotage);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $ecmtPermitApplication->shouldReceive('updateCabotage')
            ->with($cabotage)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'ECMT Permit Application cabotage updated'
            ],
            $result->getMessages()
        );

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('cabotage')
        );
    }
}
