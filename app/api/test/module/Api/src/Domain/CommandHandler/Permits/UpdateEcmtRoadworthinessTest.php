<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtRoadworthiness;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtRoadworthiness as UpdateEcmtRoadworthinessCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class UpdateEcmtRoadworthinessTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new UpdateEcmtRoadworthiness();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 15;
        $roadworthiness = 1;

        $command = UpdateEcmtRoadworthinessCmd::create(
            [
                'id' => $id,
                'roadworthiness' => $roadworthiness
            ]
        );

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($id);
        $ecmtPermitApplication->shouldReceive('updateRoadworthiness')
            ->with($roadworthiness)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($id, $result->getId('id'));
        $this->assertEquals(
            [
                'ECMT Permit Application roadworthiness updated'
            ],
            $result->getMessages()
        );
    }
}
