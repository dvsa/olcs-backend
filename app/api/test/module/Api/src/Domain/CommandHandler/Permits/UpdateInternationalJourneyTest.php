<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateInternationalJourney;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

/**
 * Class UpdateInternationalJourneyTest
 */
class UpdateInternationalJourneyTest extends CommandHandlerTestCase
{
    protected $refData = [
        RefData::INTER_JOURNEY_60_90
    ];

    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->sut = new UpdateInternationalJourney();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 22;
        $internationalJourney = RefData::INTER_JOURNEY_60_90;

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $ecmtPermitApplication->shouldReceive('updateInternationalJourneys')
            ->with($this->refData[$internationalJourney])
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
            ->andReturn(RefData::INTER_JOURNEY_60_90);

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
