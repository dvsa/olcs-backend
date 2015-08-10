<?php

/**
 * Create Psv Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreatePsvVehicle as CommandHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command\Application\CreatePsvVehicle as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Psv Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreatePsvVehicleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Vehicle', Repository\Vehicle::class);
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class)->makePartial();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Entity\Vehicle\Vehicle::PSV_TYPE_SMALL
        ];

        parent::initReferences();
    }

    public function testHandleCommandVrmExists()
    {
        $command = Cmd::create(
            [
                'application' => 111,
                'vrm' => 'AA11AAA'
            ]
        );

        /** @var Entity\Vehicle\Vehicle $vehicle1 */
        $vehicle1 = m::mock(Entity\Vehicle\Vehicle::class)->makePartial();
        $vehicle1->setVrm('AA11AAA');

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle1 */
        $licenceVehicle1 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $licenceVehicle1->setVehicle($vehicle1);

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($licenceVehicle1);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles);

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandVrmDoesntExist()
    {
        $command = Cmd::create(
            [
                'application' => 111,
                'vrm' => 'AA11AAA',
                'makeModel' => 'Foo',
                'isNovelty' => 'Y',
                'type' => 'small',
                'receivedDate' => '2015-01-01'
            ]
        );

        /** @var Entity\Vehicle\Vehicle $vehicle1 */
        $vehicle1 = m::mock(Entity\Vehicle\Vehicle::class)->makePartial();
        $vehicle1->setVrm('BB11BBB');

        /** @var Entity\Licence\LicenceVehicle $licenceVehicle1 */
        $licenceVehicle1 = m::mock(Entity\Licence\LicenceVehicle::class)->makePartial();
        $licenceVehicle1->setVehicle($vehicle1);

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($licenceVehicle1);

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles);

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $savedVehicle = null;

        $this->repoMap['Vehicle']->shouldReceive('save')
            ->once()
            ->with(m::type(Entity\Vehicle\Vehicle::class))
            ->andReturnUsing(
                function (Entity\Vehicle\Vehicle $vehicle) use (&$savedVehicle) {
                    $savedVehicle = $vehicle;
                    $vehicle->setId(123);
                    $this->assertEquals('AA11AAA', $vehicle->getVrm());
                    $this->assertEquals('Foo', $vehicle->getMakeModel());
                    $this->assertEquals('Y', $vehicle->getIsNovelty());
                    $this->assertSame($this->refData[Entity\Vehicle\Vehicle::PSV_TYPE_SMALL], $vehicle->getPsvType());
                }
            );

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->once()
            ->with(m::type(Entity\Licence\LicenceVehicle::class))
            ->andReturnUsing(
                function (Entity\Licence\LicenceVehicle $licenceVehicle) use ($licence, $application, &$savedVehicle) {
                    $licenceVehicle->setId(321);
                    $this->assertEquals('2015-01-01', $licenceVehicle->getReceivedDate()->format('Y-m-d'));
                    $this->assertSame($savedVehicle, $licenceVehicle->getVehicle());
                    $this->assertSame($licence, $licenceVehicle->getLicence());
                    $this->assertSame($application, $licenceVehicle->getApplication());
                }
            );

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $data = [
            'id' => 111,
            'section' => 'vehiclesPsv'
        ];
        $result1 = new Result();
        $result1->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoOtherVehicles()
    {
        $command = Cmd::create(
            [
                'application' => 111,
                'vrm' => 'AA11AAA',
                'makeModel' => 'Foo',
                'isNovelty' => 'Y',
                'type' => 'small',
                'receivedDate' => '2015-01-01'
            ]
        );

        $activeVehicles = new ArrayCollection();

        /** @var Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles);

        /** @var Entity\Application\Application $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $savedVehicle = null;

        $this->repoMap['Vehicle']->shouldReceive('save')
            ->once()
            ->with(m::type(Entity\Vehicle\Vehicle::class))
            ->andReturnUsing(
                function (Entity\Vehicle\Vehicle $vehicle) use (&$savedVehicle) {
                    $savedVehicle = $vehicle;
                    $vehicle->setId(123);
                    $this->assertEquals('AA11AAA', $vehicle->getVrm());
                    $this->assertEquals('Foo', $vehicle->getMakeModel());
                    $this->assertEquals('Y', $vehicle->getIsNovelty());
                    $this->assertSame($this->refData[Entity\Vehicle\Vehicle::PSV_TYPE_SMALL], $vehicle->getPsvType());
                }
            );

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->once()
            ->with(m::type(Entity\Licence\LicenceVehicle::class))
            ->andReturnUsing(
                function (Entity\Licence\LicenceVehicle $licenceVehicle) use ($licence, $application, &$savedVehicle) {
                    $licenceVehicle->setId(321);
                    $this->assertEquals('2015-01-01', $licenceVehicle->getReceivedDate()->format('Y-m-d'));
                    $this->assertSame($savedVehicle, $licenceVehicle->getVehicle());
                    $this->assertSame($licence, $licenceVehicle->getLicence());
                    $this->assertSame($application, $licenceVehicle->getApplication());
                }
            );

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Entity\User\Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $data = [
            'id' => 111,
            'section' => 'vehiclesPsv'
        ];
        $result1 = new Result();
        $result1->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
