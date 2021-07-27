<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateOperatingCentres as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;

/**
 * @see \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateOperatingCentres
 */
class UpdateOperatingCentresTest extends CommandHandlerTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    protected const TOT_AUTH_VEHICLES_COMMAND_PROPERTY = 'totAuthVehicles';
    protected const ID_COMMAND_PROPERTY = 'id';
    protected const A_NUMBER_OF_AUTHORIZED_VEHICLES = 2;
    protected const DEFAULT_NUMBER_OF_AUTHORIZED_LGV_VEHICLES = null;
    protected const LICENCE_ID = 1;

    /**
     * @test
     */
    public function handleCommand_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'handleCommand']);
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandPsvInvalid()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(true);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandGvInvalid()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandPsvValid()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 4,
            'totAuthVehicles' => 10,
            ''
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(true);
        $licence->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $licence->setOperatingCentres($locs);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('save')
            ->once()
            ->with($licence);

        $this->expectedLicenceCacheClear($licence);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence record updated']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandGvValid()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'enforcementArea' => 'A111'
        ];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);

        $locs = new ArrayCollection();
        $locs->add($loc);

        $ta = m::mock(TrafficArea::class)->makePartial();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->setOperatingCentres($locs);
        $licence->setTrafficArea($ta);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'maxVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($licence, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->repoMap['Licence']->shouldReceive('save')
            ->once()
            ->with($licence);

        $this->expectedLicenceCacheClear($licence);
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence record updated']
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertSame($this->references[EnforcementArea::class]['A111'], $licence->getEnforcementArea());
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_SavesALicence()
    {
        // Setup
        $this->setUpSut();
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => static::LICENCE_ID]);

        // Expect
        $this->licenceRepository()->expects('save')->withArgs(function ($licence) {
            $this->assertInstanceOf(Licence::class, $licence);
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_SavesALicence
     */
    public function handleCommand_SetsTotAuthVehicles_ForGoodsVehicleOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $this->injectEntity($this->licenceForGoodsLicence($id = static::LICENCE_ID));
        $command = Cmd::create([
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => static::A_NUMBER_OF_AUTHORIZED_VEHICLES,
            static::ID_COMMAND_PROPERTY => $id,
        ]);

        // Expect
        $this->licenceRepository()->expects('save')->withArgs(function (Licence $licence) {
            $this->assertSame(static::A_NUMBER_OF_AUTHORIZED_VEHICLES, $licence->getTotAuthVehicles());
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_SavesALicence
     */
    public function handleCommand_SetsTotAuthVehicles_ForPsvOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $this->injectEntity($this->licenceForPsv($id = static::LICENCE_ID));
        $command = Cmd::create([
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => static::A_NUMBER_OF_AUTHORIZED_VEHICLES,
            static::ID_COMMAND_PROPERTY => $id,
        ]);

        // Expect
        $this->licenceRepository()->expects('save')->withArgs(function (Licence $licence) {
            $this->assertSame(static::A_NUMBER_OF_AUTHORIZED_VEHICLES, $licence->getTotAuthVehicles());
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut()
    {
        $this->sut = new CommandHandler();

        if (null !== $this->serviceManager()) {
            $this->sut->createService($this->commandHandlerManager());
        }
    }

    protected function setUpDefaultServices()
    {
        $this->serviceManager()->setService('UpdateOperatingCentreHelper', $this->setUpMockService(UpdateOperatingCentreHelper::class));
        $this->setUpAbstractCommandHandlerServices();
        $this->licenceRepository();
    }

    /**
     * @return m\MockInterface|Repository\Licence
     */
    protected function licenceRepository(): m\MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('Licence')) {
            $instance = $this->setUpMockService(Repository\Licence::class);

            // Inject default licence instance
            $instance->allows('fetchUsingId')->andReturnUsing(function ($id) {
                return $this->licenceForGoodsLicence($id);
            })->byDefault();

            $repositoryServiceManager->setService('Licence', $instance);
        }
        return $repositoryServiceManager->get('Licence');
    }

    /**
     * @param object $entity
     */
    protected function injectEntity(object $entity)
    {
        assert(is_callable([$entity, 'getId']));
        $this->licenceRepository()
            ->allows('fetchUsingId')
            ->withArgs(function (Cmd $command) use ($entity) {
                return $command->getId() === $entity->getId();
            })
            ->andReturn($entity)
            ->byDefault();
    }

    /**
     * @param mixed $id
     * @return Licence
     */
    protected function licenceForPsv($id): Licence
    {
        $instance = new Licence($this->organisation(), new RefData(Licence::LICENCE_STATUS_VALID));
        $instance->setId($id);
        $instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_PSV));
        return $instance;
    }

    /**
     * @param mixed $id
     * @return Licence
     */
    protected function licenceForGoodsLicence($id): Licence
    {
        $instance = new Licence($this->organisation(), new RefData(Licence::LICENCE_STATUS_VALID));
        $instance->setId($id);
        $instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        return $instance;
    }

    /**
     * @return Organisation
     */
    protected function organisation(): Organisation
    {
        return new Organisation();
    }

    /**
     * @deprecated Use new test format
     */
    protected function setUpLegacy()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        $this->mockedSmServices['UpdateOperatingCentreHelper'] = m::mock(UpdateOperatingCentreHelper::class);
        $this->mockedSmServices[CacheEncryption::class] = m::mock(CacheEncryption::class);

        parent::setUp();
    }

    /**
     * @deprecated Use new test format
     */
    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            EnforcementArea::class => [
                'A111' => m::mock(EnforcementArea::class)
            ]
        ];

        parent::initReferences();
    }
}
