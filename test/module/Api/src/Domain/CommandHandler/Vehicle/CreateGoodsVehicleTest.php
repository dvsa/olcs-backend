<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Vehicle;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\RequiresConfirmationException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle\CreateGoodsVehicle;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Api\Domain\Repository\Vehicle as VehicleRepo;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateGoodsVehicleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateGoodsVehicle();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);
        $this->mockRepo('Vehicle', VehicleRepo::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandAlreadyExists()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        /** @var Vehicle $vehicle */
        $vehicle = m::mock(Vehicle::class)->makePartial();
        $vehicle->setVrm('ABC123');

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->setVehicle($vehicle);

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($licenceVehicle);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandVrmSection26()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $vehicle1 = new Vehicle();
        $vehicle1->setSection26(true);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(111)->once()->andReturn($licence);
        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([$vehicle1]);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertSame(Vehicle::ERROR_VRM_HAS_SECTION_26, $e->getMessages()[0]);
        }
    }

    public function testHandleCommandRequiredConfirmationSelfserve()
    {
        $this->setExpectedException(RequiresConfirmationException::class, 'Vehicle exists on other licence');

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        /** @var Licence $otherLicence1 */
        $otherLicence1 = m::mock(Licence::class)->makePartial();
        $otherLicences = [$licence, $otherLicence1];

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $this->repoMap['Licence']->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandRequiredConfirmationSelfserveIdentifyDuplicates()
    {
        $this->setExpectedException(RequiresConfirmationException::class, 'Vehicle exists on other licence');

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'identifyDuplicates' => true,
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($licenceVehicle);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([]);
        $this->repoMap['LicenceVehicle']->shouldReceive('fetchDuplicates')
            ->with($licence, 'ABC123', false)
            ->andReturn($licenceVehicles);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandRequiredConfirmationInternal()
    {
        $this->setExpectedException(RequiresConfirmationException::class, '["OB12345678","APP-111"]');

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        /** @var Application $application2 */
        $application2 = m::mock(Application::class)->makePartial();
        $application2->setId(111);

        $applications2 = new ArrayCollection();
        $applications2->add($application2);

        /** @var Vehicle $vehicle */
        $vehicle = m::mock(Vehicle::class)->makePartial();
        $vehicle->setVrm('ABC456');

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->setVehicle($vehicle);

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($licenceVehicle);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        /** @var Licence $otherLicence1 */
        $otherLicence1 = m::mock(Licence::class)->makePartial();
        $otherLicence1->setLicNo('OB12345678');

        /** @var Licence $otherLicence2 */
        $otherLicence2 = m::mock(Licence::class)->makePartial();
        $otherLicence2->setApplications($applications2);

        $otherLicences = [$licence, $otherLicence1, $otherLicence2];

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $this->repoMap['Licence']->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandRequiredConfirmationInternalIdentifyDuplicates()
    {
        $this->setExpectedException(RequiresConfirmationException::class, '["OB12345678","APP-111"]');

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'identifyDuplicates' => true,
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        /** @var Application $application2 */
        $application2 = m::mock(Application::class)->makePartial();
        $application2->setId(111);

        $applications2 = new ArrayCollection();
        $applications2->add($application2);

        /** @var Vehicle $vehicle */
        $vehicle = m::mock(Vehicle::class)->makePartial();
        $vehicle->setVrm('ABC456');

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->setVehicle($vehicle);

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($licenceVehicle);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        /** @var Licence $otherLicence1 */
        $otherLicence1 = m::mock(Licence::class)->makePartial();
        $otherLicence1->setLicNo('OB12345678');

        /** @var Licence $otherLicence2 */
        $otherLicence2 = m::mock(Licence::class)->makePartial();
        $otherLicence2->setApplications($applications2);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->once()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var LicenceVehicle $licenceVehicle1 */
        $licenceVehicle1 = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle1->setLicence($otherLicence1);

        /** @var LicenceVehicle $licenceVehicle2 */
        $licenceVehicle2 = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle2->setLicence($otherLicence2);

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($licenceVehicle1);
        $licenceVehicles->add($licenceVehicle2);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchDuplicates')
            ->with($licence, 'ABC123', false)
            ->andReturn($licenceVehicles);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $otherLicences = [];

        $vehicle = new Vehicle();
        $vehicle->setId(123);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([$vehicle]);
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle)->once()->andReturn([$vehicle]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);
        $this->assertSame($vehicle, $savedLicenceVehicle->getVehicle());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals(100, $vehicle->getPlatedWeight());
    }

    public function testHandleCommandIdentifyDuplicates()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'identifyDuplicates' => true,
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var Vehicle $savedVehicle */
        $savedVehicle = null;
        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['Vehicle']->shouldReceive('save')
            ->with(m::type(Vehicle::class))
            ->andReturnUsing(
                function (Vehicle $vehicle) use (&$savedVehicle) {
                    $vehicle->setId(123);
                    $savedVehicle = $vehicle;
                }
            );

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchDuplicates')
            ->with($licence, 'ABC123', false)
            ->andReturn([])
            ->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $savedVehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);

        $this->assertSame($savedVehicle, $savedLicenceVehicle->getVehicle());
        $this->assertEquals('ABC123', $savedVehicle->getVrm());
        $this->assertEquals(100, $savedVehicle->getPlatedWeight());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
    }

    public function testHandleCommandAlternative()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $otherLicences = [$licence];

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        /** @var Vehicle $savedVehicle */
        $savedVehicle = null;
        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['Vehicle']->shouldReceive('save')
            ->with(m::type(Vehicle::class))
            ->andReturnUsing(
                function (Vehicle $vehicle) use (&$savedVehicle) {
                    $vehicle->setId(123);
                    $savedVehicle = $vehicle;
                }
            );

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $savedVehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);

        $this->assertSame($savedVehicle, $savedLicenceVehicle->getVehicle());
        $this->assertEquals('ABC123', $savedVehicle->getVrm());
        $this->assertEquals(100, $savedVehicle->getPlatedWeight());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
    }

    public function testHandleCommandIdentifyDuplicatesAlternative()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'identifyDuplicates' => true,
            'confirm' => true,
            'applicationId' => null
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->andReturn($activeVehicles);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var Vehicle $savedVehicle */
        $savedVehicle = null;
        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['Vehicle']->shouldReceive('save')
            ->with(m::type(Vehicle::class))
            ->andReturnUsing(
                function (Vehicle $vehicle) use (&$savedVehicle) {
                    $vehicle->setId(123);
                    $savedVehicle = $vehicle;
                }
            );

        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->shouldReceive('updateDuplicateMark')->once();

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($licenceVehicle);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchDuplicates')
            ->with($licence, 'ABC123', false)
            ->andReturn($licenceVehicles)
            ->shouldReceive('save')
            ->with($licenceVehicle)
            ->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $savedVehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);

        $this->assertSame($savedVehicle, $savedLicenceVehicle->getVehicle());
        $this->assertEquals('ABC123', $savedVehicle->getVrm());
        $this->assertEquals(100, $savedVehicle->getPlatedWeight());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
    }

    public function testHandleCommandForApplication()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => 999
        ];
        $command = Cmd::create($data);

        $activeVehicle = m::mock()
            ->shouldReceive('getVehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('getVrm')
                ->andReturn('FOO')
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('getApplication')
            ->andReturn(null)
            ->once()
            ->getMock();

        $activeVehicles = new ArrayCollection();
        $activeVehicles->add($activeVehicle);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles)
            ->once();

        $otherLicences = [];

        $vehicle = new Vehicle();
        $vehicle->setId(123);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([$vehicle]);
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle)->once()->andReturn([$vehicle]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);
        $this->assertSame($vehicle, $savedLicenceVehicle->getVehicle());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals(100, $vehicle->getPlatedWeight());
    }

    public function testHandleCommandForApplicationNoActivevehicles()
    {
        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => 999
        ];
        $command = Cmd::create($data);

        $activeVehicles = new ArrayCollection();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles)
            ->once();

        $otherLicences = [];

        $vehicle = new Vehicle();
        $vehicle->setId(123);

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')->with('ABC123')->twice()->andReturn([$vehicle]);
        $this->repoMap['Vehicle']->shouldReceive('save')->with($vehicle)->once()->andReturn([$vehicle]);
        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->shouldReceive('fetchByVrm')
            ->with('ABC123', true)
            ->andReturn($otherLicences);

        /** @var LicenceVehicle $savedLicenceVehicle */
        $savedLicenceVehicle = null;

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->with(m::type(LicenceVehicle::class))
            ->andReturnUsing(
                function (LicenceVehicle $licenceVehicle) use (&$savedLicenceVehicle) {
                    $licenceVehicle->setId(321);
                    $savedLicenceVehicle = $licenceVehicle;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'vehicle' => 123,
                'licenceVehicle' => 321
            ],
            'messages' => [
                'Vehicle created',
                'Licence Vehicle created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertInstanceOf(LicenceVehicle::class, $savedLicenceVehicle);
        $this->assertSame($vehicle, $savedLicenceVehicle->getVehicle());

        $this->assertSame($licence, $savedLicenceVehicle->getLicence());
        $this->assertEquals('2015-01-01', $savedLicenceVehicle->getSpecifiedDate()->format('Y-m-d'));
        $this->assertEquals('2015-02-02', $savedLicenceVehicle->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals(100, $vehicle->getPlatedWeight());
    }

    public function testHandleCommandForApplicationWithException()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => 999
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')
            ->with('ABC123')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->once();

        $activeVehicles = new ArrayCollection();

        $licenceVehicle = m::mock()
            ->shouldReceive('getApplication')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(999)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('getVehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('getVrm')
                ->andReturn('ABC123')
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $activeVehicles->add($licenceVehicle);

        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles)
            ->once();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandForApplicationWithExceptionAlternative()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => '2015-01-01',
            'receivedDate' => '2015-02-02',
            'applicationId' => 999
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $this->repoMap['Vehicle']->shouldReceive('fetchByVrm')
            ->with('ABC123')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence)
            ->once();

        $activeVehicles = new ArrayCollection();

        $licenceVehicle = m::mock()
            ->shouldReceive('getApplication')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(888)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getVehicle')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVrm')
                    ->andReturn('ABC123')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getSpecifiedDate')
            ->andReturn('01/01/2017')
            ->once()
            ->getMock();

        $activeVehicles->add($licenceVehicle);

        $licence->shouldReceive('getActiveVehicles')
            ->with(false)
            ->andReturn($activeVehicles)
            ->once();

        $this->sut->handleCommand($command);
    }
}
