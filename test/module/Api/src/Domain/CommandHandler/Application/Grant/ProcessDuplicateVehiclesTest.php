<?php

/**
 * Process Duplicate Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\ProcessDuplicateVehicles as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessDuplicateVehicles as Cmd;

/**
 * Process Duplicate Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ProcessDuplicateVehiclesTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->repoMap['LicenceVehicle']->shouldReceive('markDuplicateVehiclesForApplication')->with($application)
            ->once()->andReturn(42);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '42 vehicle(s) marked as duplicate'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
