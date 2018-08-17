<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateInternationalJourney;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class UpdateInternationalJourneyTest extends CommandHandlerTestCase
{
    const FROM_60_TO_90_PERCENT = 2;

    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new UpdateInternationalJourney();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 22;
        $internationalJourney = self::FROM_60_TO_90_PERCENT;

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $ecmtPermitApplication->shouldReceive('updateInternationalJourneys')
            ->with($internationalJourney)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $command->shouldReceive('getInternationalJourney')
            ->andReturn($internationalJourney);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );

        $this->assertEquals(
            [],
            $result->getMessages()
        );
    }
}
