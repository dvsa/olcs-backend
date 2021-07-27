<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateOperatingCentres as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;

/**
 * @see \Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateOperatingCentres
 */
class UpdateOperatingCentresTest extends CommandHandlerTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    protected const TOT_AUTH_VEHICLES_COMMAND_PROPERTY = 'totAuthVehicles';
    protected const ID_COMMAND_PROPERTY = 'id';
    protected const A_NUMBER_OF_AUTHORIZED_VEHICLES = 2;
    protected const DEFAULT_NUMBER_OF_AUTHORIZED_LGV_VEHICLES = null;
    protected const APPLICATION_ID = 1;

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
    public function testHandleCommandPartialMissingTa()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => true,
            'partialAction' => 'add',
            'trafficArea' => null

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('addMessage')
            ->once()
            ->with('trafficArea', 'ERR_OC_TA_1')
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandPsvTooManyCommLic()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => null,
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 8

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('getTrafficArea->getId')->andReturn('TA');
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('addMessage')
            ->once()
            ->with('totCommunityLicences', 'ERR_OC_CL_1')
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(false);

        $aoc = [
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 10,
        ];

        $aocs = [
            $aoc
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

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
            'partial' => false,
            'trafficArea' => null,
            'totCommunityLicences' => 8,
            'totAuthVehicles' => 8

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ],
            [
                'action' => 'D'
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
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
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(false);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

        $data = [
            'id' => 222,
            'version' => 1,
            'trafficArea' => 'A'
        ];
        $result = new Result();
        $result->addMessage('UpdateTrafficArea');
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'UpdateTrafficArea',
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandTrafficAreaValidation()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('addMessage')
            ->with('trafficArea', 'ERROR_1', 'TA_NAME')
            ->once()
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(
            ['ERROR_1' => 'TA_NAME']
        );

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

        $data = [
            'id' => 222,
            'version' => 1,
            'trafficArea' => 'A'
        ];
        $result = new Result();
        $result->addMessage('UpdateTrafficArea');
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'UpdateTrafficArea',
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
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
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthSmallVehicles' => 4,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @depends handleCommand_IsCallable
     */
    public function testHandleCommandPsvValidVariationWithTa()
    {
        $this->setUpLegacy();
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthSmallVehicles' => 4,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $application->shouldReceive('getTrafficArea')->andReturn('anything');
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->references[EnforcementArea::class]['A111'], $licence->getEnforcementArea());
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_SavesAnApplication()
    {
        // Setup
        $this->setUpSut();
        $command = Cmd::create([static::ID_COMMAND_PROPERTY => static::APPLICATION_ID]);

        // Expect
        $this->applicationRepository()->expects('save')->withArgs(function ($application) {
            $this->assertInstanceOf(Application::class, $application);
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_SavesAnApplication
     */
    public function handleCommand_SetsTotAuthVehicles_ForGoodsVehicleOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $this->injectEntity($this->variationForGoodsLicence($id = static::APPLICATION_ID));
        $command = Cmd::create([
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => static::A_NUMBER_OF_AUTHORIZED_VEHICLES,
            static::ID_COMMAND_PROPERTY => $id,
        ]);

        // Expect
        $this->applicationRepository()->expects('save')->withArgs(function (Application $application) {
            $this->assertSame(static::A_NUMBER_OF_AUTHORIZED_VEHICLES, $application->getTotAuthVehicles());
            return true;
        });

        // Execute
        $this->sut->handleCommand($command);
    }

    /**
     * @test
     * @depends handleCommand_SavesAnApplication
     */
    public function handleCommand_SetsTotAuthVehicles_ForPsvOperatingCentre()
    {
        // Setup
        $this->setUpSut();
        $this->injectEntity($this->variationForPsvLicence($id = static::APPLICATION_ID));
        $command = Cmd::create([
            static::TOT_AUTH_VEHICLES_COMMAND_PROPERTY => static::A_NUMBER_OF_AUTHORIZED_VEHICLES,
            static::ID_COMMAND_PROPERTY => $id,
        ]);

        // Expect
        $this->applicationRepository()->expects('save')->withArgs(function (Application $application) {
            $this->assertSame(static::A_NUMBER_OF_AUTHORIZED_VEHICLES, $application->getTotAuthVehicles());
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
        $this->variationHelper();
        $this->serviceManager()->setService('UpdateOperatingCentreHelper', $this->setUpMockService(UpdateOperatingCentreHelper::class));
        $this->serviceManager()->setService('TrafficAreaValidator', $this->setUpMockService(\Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator::class));
        $this->setUpAbstractCommandHandlerServices();
        $this->applicationRepository();
    }

    /**
     * @return m\MockInterface|VariationOperatingCentreHelper
     */
    protected function variationHelper(): m\MockInterface
    {
        if (! $this->serviceManager()->has('VariationOperatingCentreHelper')) {
            $instance = $this->setUpMockService(VariationOperatingCentreHelper::class);
            $instance->allows('getListDataForApplication')->andReturn([])->byDefault();
            $this->serviceManager()->setService('VariationOperatingCentreHelper', $instance);
        }
        return $this->serviceManager()->get('VariationOperatingCentreHelper');
    }

    /**
     * @return m\MockInterface|Repository\Application
     */
    protected function applicationRepository(): m\MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('Application')) {
            $instance = $this->setUpMockService(Repository\Application::class);

            // Inject default application instance
            $instance->allows('fetchUsingId')->andReturnUsing(function (Cmd $command) {
                return $this->variationForGoodsLicence($command->getId());
            })->byDefault();

            $repositoryServiceManager->setService('Application', $instance);
        }
        return $repositoryServiceManager->get('Application');
    }

    /**
     * @param object $entity
     */
    protected function injectEntity(object $entity)
    {
        assert(is_callable([$entity, 'getId']));
        $this->applicationRepository()
            ->allows('fetchUsingId')
            ->withArgs(function (Cmd $command) use ($entity) {
                return $command->getId() === $entity->getId();
            })
            ->andReturn($entity)
            ->byDefault();
    }

    /**
     * @param int $id
     * @return Application
     */
    protected function variationForPsvLicence(int $id): Application
    {
        $instance = $this->variationForGoodsLicence($id);
        $instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_PSV));
        return $instance;
    }

    /**
     * @param int $id
     * @return Application
     */
    protected function variationForGoodsLicence(int $id): Application
    {
        $instance = new Application($this->licence(), new RefData(Application::APPLICATION_STATUS_NOT_SUBMITTED), true);
        $instance->setId($id);
        $instance->setGoodsOrPsv(new RefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        return $instance;
    }

    /**
     * @return Licence
     */
    public function licence(): Licence
    {
        return new Licence($this->organisation(), new RefData(Licence::LICENCE_STATUS_VALID));
    }

    /**
     * @return Organisation
     */
    protected function organisation(): Organisation
    {
        return new Organisation();
    }

    /**
     * @deprecated Use new test format instead
     */
    protected function setUpLegacy()
    {
        $this->setUpSut();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);
        $this->mockedSmServices['VariationOperatingCentreHelper'] = m::mock(VariationOperatingCentreHelper::class);
        $this->mockedSmServices['UpdateOperatingCentreHelper'] = m::mock(UpdateOperatingCentreHelper::class);
        $this->mockedSmServices['TrafficAreaValidator'] =
            m::mock(\Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator::class);
        parent::setUp();
    }

    /**
     * @deprecated Use new test format instead
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
