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
    public function setUp()
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

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle1 */
        $licenceVehicle1 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $licenceVehicle1->shouldReceive('getVehicle->getVrm')->andReturn('AB1');

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle2 */
        $licenceVehicle2 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $licenceVehicle2->shouldReceive('getVehicle->getVrm')->andReturn('AB2');

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($licenceVehicle1);
        $licenceVehicles->add($licenceVehicle2);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setLicenceVehicles($licenceVehicles);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        /** @var Entity\Licence\LicenceVehicle $duplicate */
        $duplicate = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $duplicate->shouldReceive('markAsDuplicate')->once();

        $duplicates = [
            $duplicate
        ];

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchDuplicates')
            ->with($licence, 'AB1')
            ->andReturn(null)
            ->shouldReceive('fetchDuplicates')
            ->with($licence, 'AB2')
            ->andReturn($duplicates)
            ->shouldReceive('save')
            ->once()
            ->with($duplicate);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 vehicle(s) marked as duplicate'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
