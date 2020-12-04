<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantPsv;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant as GrantApplicationCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService;
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Transfer\Command\Application\Grant as Cmd;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantTest extends CommandHandlerTestCase
{
    const SERVICE_TRANSACTION_MANAGER = 'TransactionManager';
    const SERVICE_REPOSITORY_MANAGER = 'RepositoryServiceManager';
    const SERVICE_VALIDATION = 'ApplicationGrantValidationService';
    const SERVICE_QUERY_HANDLER = 'QueryHandlerManager';
    const REPOSITORY_APPLICATION = 'Application';

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->sut = new GrantApplicationCommandHandler();
        $this->mockRepo('Application', Application::class);
        $this->mockedSmServices['ApplicationGrantValidationService'] = m::mock();
        parent::setUp();
    }

    public function testHandleCommandIsDefined()
    {
        $command = new GrantApplicationCommandHandler();
        $this->assertIsCallable([$command, 'handleCommand']);
    }

    public function testHandleCommandWithException()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'shouldCreateInspectionRequest' => 'Y',
            'dueDate' => null
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithFailedValidation()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'N',
            'dueDate' => null
        ];
        $command = Cmd::create($data);

        $application = m::mock(ApplicationEntity::class)->makePartial();
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->mockedSmServices['ApplicationGrantValidationService']->shouldReceive('validate')->with($application)
            ->andReturn(['MESSAGE1', 'MESSAGE2']);

        try {
            $this->sut->handleCommand($command);

            $this->fail('Exception should have been thrown');
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertSame(['MESSAGE1', 'MESSAGE2'], $e->getMessages());
        }
    }

    public function testHandleCommandGoods()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'N',
            'dueDate' => null,
            'id' => 111,
            'notes' => null,
        ];

        $command = Cmd::create(array_merge($data, ['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]));

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('isGoods')
            ->andReturn(true);
        $application->shouldReceive('getTrafficArea->getId')
            ->andReturn('TA');
        $application->shouldReceive('setRequestInspection')
            ->with(false)
            ->once();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->once()
            ->shouldReceive('save')
            ->times(2);

        $this->mockedSmServices['ApplicationGrantValidationService']->shouldReceive('validate')->with($application)
            ->andReturn([]);

        $result1 = new Result();
        $result1->addMessage('GrantGoods');
        $this->expectedSideEffect(GrantGoods::class, $data, $result1);

        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 111, 'trafficArea' => 'TA', 'publicationSection' => 4],
            new Result()
        );
        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 111],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantGoods'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsv()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'N',
            'dueDate' => null,
            'id' => 111,
            'notes' => null
        ];

        $command = Cmd::create(array_merge($data, ['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]));

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('isGoods')
            ->andReturn(false);
        $application->shouldReceive('getTrafficArea->getId')
            ->andReturn('TA');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->mockedSmServices['ApplicationGrantValidationService']->shouldReceive('validate')->with($application)
            ->andReturn([]);

        $result1 = new Result();
        $result1->addMessage('GrantPsv');
        $this->expectedSideEffect(GrantPsv::class, $data, $result1);

        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 111, 'trafficArea' => 'TA', 'publicationSection' => 4],
            new Result()
        );
        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 111],
            new Result()
        );

        $this->repoMap['Application']->shouldReceive('save')->times(1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantPsv'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvWithInspectionRequest()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'Y',
            'dueDate' => 3,
            'id' => 111,
            'notes' => 'Notes go here'
        ];

        $command = Cmd::create(array_merge($data, ['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]));

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('isGoods')
            ->andReturn(false);
        $application->shouldReceive('getTrafficArea->getId')
            ->andReturn('TA');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->mockedSmServices['ApplicationGrantValidationService']->shouldReceive('validate')->with($application)
            ->andReturn([]);

        $result1 = new Result();
        $result1->addMessage('GrantPsv');
        $this->expectedSideEffect(GrantPsv::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('CreateFromGrant');
        $data = [
            'application' => 111,
            'duePeriod' => 3,
            'caseworkerNotes' => 'Notes go here'
        ];
        $this->expectedSideEffect(CreateFromGrant::class, $data, $result2);

        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 111, 'trafficArea' => 'TA'],
            new Result()
        );
        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 111],
            new Result()
        );

        $this->repoMap['Application']->shouldReceive('save')->times(1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantPsv',
                'CreateFromGrant'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandGoodsWithInspectionRequest()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'Y',
            'dueDate' => 3,
            'id' => 111,
            'notes' => 'foo'
        ];

        $command = Cmd::create(array_merge($data, ['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]));

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);

        $application->shouldReceive('isGoods')
            ->andReturn(true);
        $application->shouldReceive('getTrafficArea->getId')
            ->andReturn('TA');
        $application->shouldReceive('setRequestInspection')
            ->with(true)
            ->once()
            ->shouldReceive('setRequestInspectionDelay')
            ->with(3)
            ->once()
            ->shouldReceive('setRequestInspectionComment')
            ->with('foo')
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->once()
            ->shouldReceive('save')
            ->times(2);

        $this->mockedSmServices['ApplicationGrantValidationService']->shouldReceive('validate')->with($application)
            ->andReturn([]);

        $result1 = new Result();
        $result1->addMessage('GrantGoods');
        $this->expectedSideEffect(GrantGoods::class, $data, $result1);

        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 111, 'trafficArea' => 'TA', 'publicationSection' => 4],
            new Result()
        );
        $this->expectedSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 111],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantGoods',
                'Inspection request details saved'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandSetsApplicationGrantAuthority()
    {
        // Prepare test
        $command = Cmd::create(['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]);
        $expectedRefData = new RefData(RefData::GRANT_AUTHORITY_DELEGATED);

        $application = $this->getMockBuilder(ApplicationEntity::class)->disableOriginalConstructor()->getMock();

        $applicationRepository = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $applicationRepository->method('fetchUsingId')->with($command)->willReturn($application);
        $applicationRepository->method('getRefdataReference')->with(RefData::GRANT_AUTHORITY_DELEGATED)->willReturn($expectedRefData);

        $repositoryManager = $this->newMockRepositoryManager([static::REPOSITORY_APPLICATION => $applicationRepository]);
        $serviceLocator = $this->newMockServiceLocator([static::SERVICE_REPOSITORY_MANAGER => $repositoryManager]);

        // Set expectations
        $application->expects($this->atLeastOnce())->method('setGrantAuthority')->with($expectedRefData);

        // Execute test
        $commandHandler = new GrantApplicationCommandHandler();
        $commandHandler->createService($serviceLocator);
        $commandHandler->handleCommand($command);
    }

    public function testHandleCommandSetsApplicationGrantAuthorityBeforeApplicationIsSaved()
    {
        // Prepare test
        $command = Cmd::create(['grantAuthority' => RefData::GRANT_AUTHORITY_DELEGATED]);
        $expectedRefData = new RefData(RefData::GRANT_AUTHORITY_DELEGATED);

        $application = $this->getMockBuilder(ApplicationEntity::class)->disableOriginalConstructor()->getMock();
        $application->method('isGoods')->willReturn(true);

        $applicationRepository = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $applicationRepository->method('fetchUsingId')->with($command)->willReturn($application);
        $applicationRepository->method('getRefdataReference')->with(RefData::GRANT_AUTHORITY_DELEGATED)->willReturn($expectedRefData);

        $repositoryManager = $this->newMockRepositoryManager([static::REPOSITORY_APPLICATION => $applicationRepository]);
        $serviceLocator = $this->newMockServiceLocator([static::SERVICE_REPOSITORY_MANAGER => $repositoryManager]);

        // Set expectations
        $application->expects($setGrantAuthorityMatcher = $this->atLeastOnce())->method('setGrantAuthority')->with($expectedRefData);
        $application->expects($this->atLeastOnce())->method('setGrantAuthority')->with($expectedRefData);
        $applicationRepository->expects($this->atLeastOnce())->method('save')->with(
            $this->callback(function ($actualApplication) use ($setGrantAuthorityMatcher, $application) {

                // The grant authority should have been set before the application is saved
                return $setGrantAuthorityMatcher->hasBeenInvoked() && $actualApplication === $application;
            })
        );

        // Execute test
        $commandHandler = new GrantApplicationCommandHandler();
        $commandHandler->createService($serviceLocator);
        $commandHandler->handleCommand($command);
    }

    /**
     * Creates a new mock service locator.
     *
     * @param array $services
     * @param bool $overrideHandleCommandMethod
     * @return MockObject
     */
    protected function newMockServiceLocator(array $services = [], bool $overrideHandleCommandMethod = true)
    {
        $transactionManager = $services[static::SERVICE_TRANSACTION_MANAGER] ?? $this->getMockBuilder(TransactionManagerInterface::class)
                ->disableOriginalConstructor()->getMock();
        $repositoryServiceManager = $services[static::SERVICE_REPOSITORY_MANAGER] ?? $this->newMockRepositoryManager();
        $validationService = $services[static::SERVICE_VALIDATION] ?? $this->newMockValidationService();
        $queryHandler = $services[static::SERVICE_QUERY_HANDLER] ?? $this->getMockBuilder(QueryHandlerInterface::class)
                ->disableOriginalConstructor()->getMock();
        $serviceLocator = $this->getMockBuilder(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getServiceLocator', 'handleCommand'])
            ->onlyMethods(['get', 'has'])
            ->getMock();
        $serviceLocator->expects($this->any())->method('getServiceLocator')->willReturnSelf();
        $serviceLocator->expects($this->any())->method('get')->willReturn($this->returnCallback(function ($class) use (
            $transactionManager,
            $repositoryServiceManager,
            $validationService,
            $queryHandler
        ) {
            switch ($class) {
                case static::SERVICE_TRANSACTION_MANAGER:
                    return $transactionManager;
                case static::SERVICE_REPOSITORY_MANAGER:
                    return $repositoryServiceManager;
                case static::SERVICE_VALIDATION:
                    return $validationService;
                case static::SERVICE_QUERY_HANDLER:
                    return $queryHandler;
                default:
                    return null;
            }
        }));

        if (true === $overrideHandleCommandMethod) {
            // By default we will override this method as the command handler is not injected in. Instead the abstract
            // command handler assumes that the service locator is also a command handler.
            $serviceLocator->expects($this->any())->method('handleCommand')->willReturn($this->returnCallback(function () {
                return new Result();
            }));
        }

        return $serviceLocator;
    }

    /**
     * Creates a new mock repository manager.
     *
     * @param array $repositories
     * @return MockObject
     */
    protected function newMockRepositoryManager(array $repositories = [])
    {
        $applicationRepository = $repositories[static::REPOSITORY_APPLICATION] ?? $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $instance = $this->getMockBuilder(RepositoryServiceManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();
        $instance->expects($this->any())->method('get')->willReturn($this->returnCallback(function ($class) use ($applicationRepository) {
            switch ($class) {
                case 'Application':
                    return $applicationRepository;
                default:
                    return null;
            }
        }));
        return $instance;
    }

    /**
     * Creates a new mock validation service.
     *
     * @param array $errors
     * @return MockObject
     */
    protected function newMockValidationService(array $errors = [])
    {
        $instance = $this->getMockBuilder(GrantValidationService::class)->disableOriginalConstructor()->getMock();
        $instance->expects($this->any())->method('validate')->willReturn($errors);
        return $instance;
    }
}
