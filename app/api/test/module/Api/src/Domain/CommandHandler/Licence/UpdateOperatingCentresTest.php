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
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\Api\Entity\Licence\LicenceBuilder;
use ZfcRbac\Service\AuthorizationService;
use Hamcrest\Core\AllOf;
use Hamcrest\Arrays\IsArrayContainingKeyValuePair;
use Dvsa\OlcsTest\Api\Domain\Repository\MocksLicenceRepositoryTrait;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\ProvidesOperatingCentreVehicleAuthorizationConstraintsTrait;

/**
 * @see \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateOperatingCentres
 */
class UpdateOperatingCentresTest extends CommandHandlerTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;
    use ProvidesOperatingCentreVehicleAuthorizationConstraintsTrait;
    use MocksLicenceRepositoryTrait;

    protected const TOT_AUTH_VEHICLES_COMMAND_PROPERTY = 'totAuthVehicles';
    protected const ID_COMMAND_PROPERTY = 'id';
    protected const A_NUMBER_OF_VEHICLES = 2;
    protected const AN_ID = 1;
    protected const VALIDATION_MESSAGES = ['A VALIDATION MESSAGE KEY' => 'A VALIDATION MESSAGE VALUE'];
    protected const ONE_VEHICLE = 1;
    protected const NO_VEHICLES = 0;

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

        $expectedTotals = AllOf::allOf(
            IsArrayContainingKeyValuePair::hasKeyValuePair('noOfOperatingCentres', 1),
            IsArrayContainingKeyValuePair::hasKeyValuePair('minTrailerAuth', 10),
            IsArrayContainingKeyValuePair::hasKeyValuePair('maxTrailerAuth', 10)
        );

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

        $expectedTotals = AllOf::allOf(
            IsArrayContainingKeyValuePair::hasKeyValuePair('noOfOperatingCentres', 1),
            IsArrayContainingKeyValuePair::hasKeyValuePair('minTrailerAuth', 10),
            IsArrayContainingKeyValuePair::hasKeyValuePair('maxTrailerAuth', 10)
        );

        $this->mockedSmServices['UpdateOperatingCentreHelper']
            ->shouldIgnoreMissing()
            ->shouldReceive('validateTotalAuthTrailers')
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

        $expectedTotals = AllOf::allOf(
            IsArrayContainingKeyValuePair::hasKeyValuePair('noOfOperatingCentres', 1),
            IsArrayContainingKeyValuePair::hasKeyValuePair('minTrailerAuth', 10),
            IsArrayContainingKeyValuePair::hasKeyValuePair('maxTrailerAuth', 10)
        );

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

        $expectedTotals = AllOf::allOf(
            IsArrayContainingKeyValuePair::hasKeyValuePair('noOfOperatingCentres', 1),
            IsArrayContainingKeyValuePair::hasKeyValuePair('minTrailerAuth', 10),
            IsArrayContainingKeyValuePair::hasKeyValuePair('maxTrailerAuth', 10)
        );

        $this->mockedSmServices['UpdateOperatingCentreHelper']
            ->shouldIgnoreMissing()
            ->shouldReceive('validateTotalAuthTrailers')
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
    public function handleCommand_SavesALicence_WithValidVehicleAuthorizations()
    {
        // Setup
        $this->setUpSut();
        $licence = LicenceBuilder::aLicence()->withValidVehicleAuthorizations()->build();
        $this->injectEntities($licence);
        $command = Cmd::create([
            static::ID_COMMAND_PROPERTY => $licence->getId(),
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => $licence->getTotAuthVehicles(),
        ]);

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
     * @depends handleCommand_SavesALicence_WithValidVehicleAuthorizations
     */
    public function handleCommand_SetsTotAuthVehicles_ForGoodsVehicleOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $licence = LicenceBuilder::aLicence()->withExtraOperatingCentreCapacityFor(static::ONE_VEHICLE)->build();
        $this->injectEntities($licence);
        $command = Cmd::create([
            static::ID_COMMAND_PROPERTY => $licence->getId(),
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => $expectedVehicleCount = $licence->getTotAuthVehicles() + static::ONE_VEHICLE,
        ]);

        // Expect
        $this->licenceRepository()->expects('save')->withArgs(function (Licence $licence) use ($expectedVehicleCount) {
            $this->assertSame($expectedVehicleCount, $licence->getTotAuthVehicles());
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_SavesALicence_WithValidVehicleAuthorizations
     */
    public function handleCommand_SetsTotAuthVehicles_ForPsvOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $licence = LicenceBuilder::aPsvLicence()->withExtraOperatingCentreCapacityFor(static::ONE_VEHICLE)->build();
        $this->injectEntities($licence);
        $command = Cmd::create([
            static::ID_COMMAND_PROPERTY => $licence->getId(),
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => $expectedVehicleCount = $licence->getTotAuthVehicles() + static::ONE_VEHICLE,
        ]);

        // Expect
        $this->licenceRepository()->expects('save')->withArgs(function (Licence $licence) use ($expectedVehicleCount) {
            $this->assertSame($expectedVehicleCount, $licence->getTotAuthVehicles());
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_ThrowsValidationException_WithAnyValidationMessages_FromTheUpdateHelper()
    {
        // Setup
        $this->overrideUpdateHelperWithMock();
        $this->setUpSut();
        $this->injectEntities(LicenceBuilder::aPsvLicence($id = static::AN_ID));
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => $id]);

        // Expect
        $this->updateHelper()->expects('getMessages')->andReturn($expectedMessages = static::VALIDATION_MESSAGES);
        $this->expectException(ValidationException::class);
        $this->expectExceptionObject(new ValidationException($expectedMessages));

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_ValidatesPsvs_WhenCommandIsNotPartial()
    {
        // Setup
        $this->overrideUpdateHelperWithMock();
        $this->setUpSut();
        $this->injectEntities($licence = LicenceBuilder::aPsvLicence()->build());
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => $licence->getId()]);

        // Expect
        $this->updateHelper()->expects('validatePsv')->withArgs(function ($arg1, $arg2) use ($licence, $command) {
            $this->assertSame($arg1, $licence);
            $this->assertSame($arg2, $command);
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_ValidatesTotAuthVehiclesVehicles_WhenCommandIsNotPartial()
    {
        // Setup
        $this->overrideUpdateHelperWithMock();
        $this->setUpSut();
        $this->injectEntities($licence = LicenceBuilder::aLicence()->build());
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => $licence->getId()]);

        // Expect
        $this->updateHelper()->expects('validateTotalAuthVehicles')->withArgs(function ($arg1, $arg2, $arg3) use ($licence, $command) {
            $this->assertSame($arg1, $licence);
            $this->assertSame($arg2, $command);
            $this->assertIsArray($arg3);
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @param array $operatingCentresVehicleCapacities
     * @param array $expectedVehicleConstraints
     * @test
     * @depends handleCommand_ValidatesTotAuthVehiclesVehicles_WhenCommandIsNotPartial
     * @dataProvider operatingCentreVehicleAuthorisationConstraintsDataProvider
     */
    public function handleCommand_ValidatesTotAuthVehiclesVehicles_WhenCommandIsNotPartial_AgainstCorrectOperatingCentreVehicleConstraints(array $operatingCentresVehicleCapacities, array $expectedVehicleConstraints)
    {
        // Setup
        $this->overrideUpdateHelperWithMock();
        $this->setUpSut();
        $licence = LicenceBuilder::aLicence(static::AN_ID)->withOperatingCentresWithCapacitiesFor($operatingCentresVehicleCapacities)->build();
        $this->injectEntities($licence);
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => $licence->getId()]);

        // Expect
        $this->updateHelper()->expects('validateTotalAuthVehicles')->withArgs(function ($arg1, $arg2, $arg3) use ($expectedVehicleConstraints, $operatingCentresVehicleCapacities) {
            $this->assertIsArray($arg3);
            foreach ($expectedVehicleConstraints as $key => $expectedTotal) {
                $this->assertSame(
                    $expectedTotal,
                    $actualTotal = $arg3[$key] ?? null,
                    sprintf('Failed to assert the value for "%s" total (%s) matched the expected value (%s)', $key, $actualTotal, $expectedTotal)
                );
            }
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
        $this->authService();
        $this->updateHelper();
        $this->setUpAbstractCommandHandlerServices();
        $this->licenceRepository();
    }

    /**
     * @return m\MockInterface|AuthorizationService
     */
    protected function authService(): m\MockInterface
    {
        if (! $this->serviceManager()->has(AuthorizationService::class)) {
            $instance = $this->setUpMockService(AuthorizationService::class);
            $this->serviceManager()->setService(AuthorizationService::class, $instance);
        }
        return $this->serviceManager()->get(AuthorizationService::class);
    }

    /**
     * @return UpdateOperatingCentreHelper|m\MockInterface
     */
    protected function updateHelper()
    {
        if (! $this->serviceManager()->has('UpdateOperatingCentreHelper')) {
            $instance = new UpdateOperatingCentreHelper();
            $instance->createService($this->serviceManager());
            $this->serviceManager()->setService('UpdateOperatingCentreHelper', $instance);
        }
        return $this->serviceManager()->get('UpdateOperatingCentreHelper');
    }

    /**
     * @return m\MockInterface|UpdateOperatingCentreHelper
     */
    protected function overrideUpdateHelperWithMock(): m\MockInterface
    {
        $instance = $this->setUpMockService(UpdateOperatingCentreHelper::class);
        $this->serviceManager()->setService('UpdateOperatingCentreHelper', $instance);
        return $this->serviceManager()->get('UpdateOperatingCentreHelper');
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
