<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Domain\ToggleAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Json\Json as ZendJson;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Command Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class CommandHandlerTestCase extends MockeryTestCase
{
    /** @var \Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler */
    protected $sut;

    /**
     * @var m\MockInterface|CommandHandlerManager
     */
    protected $commandHandler;

    /** @var  m\MockInterface | RepositoryServiceManager */
    protected $repoManager;

    /**
     * @var \Mockery\MockInterface[]
     */
    protected $repoMap = [];

    protected $sideEffects = [];

    protected $commands = [];

    protected $refData = [];

    protected $references = [];

    protected $categoryReferences = [];

    protected $subCategoryReferences = [];

    private $initRefdata = false;

    /**
     * @var \Mockery\MockInterface[]
     */
    protected $mockedSmServices = [];

    /** @var  m\MockInterface | QueryHandlerManager */
    protected $queryHandler;
    /** @var  m\MockInterface | PidIdentityProvider */
    protected $pidIdentityProvider;
    /** @var  m\MockInterface | TransactionManagerInterface */
    protected $mockTransationMngr;

    public function setUp(): void
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $this->pidIdentityProvider = m::mock(PidIdentityProvider::class);
        $this->mockTransationMngr = m::mock(TransactionManagerInterface::class);

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn($this->mockTransationMngr);
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);
        $sm->shouldReceive('get')->with(PidIdentityProvider::class)->andReturn($this->pidIdentityProvider);
        if (property_exists($this, 'submissionConfig')) {
            $sm->shouldReceive('get')->with('Config')->andReturn($this->submissionConfig);
        }
        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }
        if (array_key_exists(AuthorizationService::class, $this->mockedSmServices)) {
            $this->repoManager
                ->shouldReceive('get')
                ->with('User')
                ->andReturn(
                    m::mock(\Dvsa\Olcs\Api\Domain\Repository\User::class)
                    ->shouldReceive('fetchById')
                    ->with(PidIdentityProvider::SYSTEM_USER)
                    ->getMock()
                )
                ->getMock();
        }

        /**
         * If the handler is toggle aware, provide this for free. For more more complex testing use
         * $this->mockedSmServices in the extending class
         */
        if ($this->sut instanceof ToggleRequiredInterface || $this->sut instanceof ToggleAwareInterface
            && !array_key_exists(ToggleService::class, $this->mockedSmServices)
        ) {
            $toggleService = m::mock(ToggleService::class);
            $sm->shouldReceive('get')->with(ToggleService::class)->andReturn($toggleService);
        }

        $this->commandHandler = m::mock(CommandHandlerManager::class);
        $this->commandHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut->createService($this->commandHandler);

        $this->sideEffects = [];
        $this->commands = [];

        $this->initReferences();
    }

    protected function mockRepo($name, $class)
    {
        if (!$class instanceof m\MockInterface) {
            $class = m::mock($class);
        }

        $class
            ->shouldReceive('getRefdataReference')->andReturnUsing([$this, 'mapRefData'])
            ->shouldReceive('getReference')->andReturnUsing([$this, 'mapReference'])
            ->shouldReceive('getCategoryReference')->andReturnUsing([$this, 'mapCategoryReference'])
            ->shouldReceive('getSubCategoryReference')->andReturnUsing([$this, 'mapSubCategoryReference']);

        $this->repoMap[$name] = $class;

        return $class;
    }

    protected function initReferences()
    {
        if (!$this->initRefdata) {
            foreach ($this->refData as $id => $mock) {
                if (is_numeric($id) && is_string($mock)) {
                    $this->refData[$mock] = m::mock(RefData::class)->makePartial()->setId($mock);
                } else {
                    $mock->makePartial();
                    $mock->setId($id);
                }
            }

            foreach ($this->categoryReferences as $id => $mock) {
                $mock->makePartial();
                $mock->setId($id);
            }

            foreach ($this->subCategoryReferences as $id => $mock) {
                $mock->makePartial();
                $mock->setId($id);
            }

            foreach ($this->references as $mocks) {
                foreach ($mocks as $id => $mock) {
                    if ($mock instanceof m\MockInterface) {
                        $mock->makePartial();
                    }

                    $mock->setId($id);
                }
            }

            $this->initRefdata = true;
        }
    }

    public function tearDown(): void
    {
        $this->initRefdata = false;
        $this->assertCommandData();

        m::close();
        parent::tearDown();

        unset(
            $this->sut,
            $this->commandHandler,
            $this->repoManager,
            $this->repoMap,
            $this->sideEffects,
            $this->commands,
            $this->refData,
            $this->references,
            $this->categoryReferences,
            $this->subCategoryReferences,
            $this->initRefdata,
            $this->mockedSmServices,
            $this->pidIdentityProvider
        );
    }

    /**
     * Shortcut for queue side effects
     *
     * @param int $entityId
     * @param string $queueType
     * @param array $options
     * @param Result|null $result
     * @param string|null $processAfterDate
     */
    public function expectedQueueSideEffect(
        $entityId,
        $queueType,
        array $options = [],
        $result = null,
        $processAfterDate = null,
        $times = 1
    ) {
        if ($result === null) {
            $result = new Result();
        }

        $data = [
            'entityId' => $entityId,
            'type' => $queueType,
            'status' => QueueEntity::STATUS_QUEUED,
            'options' => ZendJson::encode($options),
            'processAfterDate' => $processAfterDate
        ];

        $this->expectedSideEffect(CreateQueueCmd::class, $data, $result, $times);
    }

    /**
     * Shortcut for email queue side effects
     *
     * @param string $emailCmdClass
     * @param array $cmdData
     * @param int $entityId
     * @param Result $result
     * @param string|null $processAfterDate
     */
    public function expectedEmailQueueSideEffect(
        $emailCmdClass,
        array $cmdData,
        $entityId,
        $result,
        $processAfterDate = null,
        $times = 1
    ) {
        $emailOptions = [
            'commandClass' => $emailCmdClass,
            'commandData' => $cmdData
        ];

        $emailData = [
            'entityId' => $entityId,
            'type' => QueueEntity::TYPE_EMAIL,
            'status' => QueueEntity::STATUS_QUEUED,
            'options' => ZendJson::encode($emailOptions),
            'processAfterDate' => $processAfterDate
        ];

        $this->expectedSideEffect(CreateQueueCmd::class, $emailData, $result, $times);
    }

    public function expectedSideEffect($class, $data, $result, $times = 1)
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->times($times)
            ->with(m::type($class), false)
            ->andReturnUsing(
                function (CommandInterface $command) use ($class, $data, $result) {
                    $this->commands[] = [$command, $data];
                    return $result;
                }
            );
    }

    public function expectedSideEffectAsSystemUser($class, $data, $result, $times = 1)
    {
        $this->pidIdentityProvider
            ->shouldReceive('setMasqueradedAsSystemUser')
            ->with(true)
            ->shouldReceive('setMasqueradedAsSystemUser')
            ->with(false)
            ->getMock();

        $this->expectedSideEffect($class, $data, $result, $times);
    }

    public function expectedSideEffectThrowsException($class, $data, $exception)
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type($class), false)
            ->andReturnUsing(
                function (CommandInterface $command) use ($class, $data, $exception) {
                    $this->commands[] = [$command, $data];
                    throw $exception;
                }
            );
    }

    public function mapRefData($key)
    {
        return isset($this->refData[$key]) ? $this->refData[$key] : null;
    }

    public function mapCategoryReference($key)
    {
        return isset($this->categoryReferences[$key]) ? $this->categoryReferences[$key] : null;
    }

    public function mapSubCategoryReference($key)
    {
        return isset($this->subCategoryReferences[$key]) ? $this->subCategoryReferences[$key] : null;
    }

    public function mapReference($class, $id)
    {
        return isset($this->references[$class][$id]) ? $this->references[$class][$id] : null;
    }

    /**
     * @NOTE must be called after the tested method has been executed
     */
    private function assertCommandData()
    {
        foreach ($this->commands as $command) {
            /** @var CommandInterface $cmd */
            list($cmd, $data) = $command;

            $cmdData = $cmd->getArrayCopy();
            $cmdDataToMatch = [];

            foreach ($data as $key => $value) {
                unset($value);
                $cmdDataToMatch[$key] = isset($cmdData[$key]) ? $cmdData[$key] : null;
            }

            $this->assertEquals($data, $cmdDataToMatch, get_class($cmd) . ' has unexpected data');
        }
    }

    protected function setupIsInternalUser($isInternalUser = true)
    {
        $this->mockedSmServices[\ZfcRbac\Service\AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->atLeast()->once()
            ->andReturn($isInternalUser);
    }

    /**
     * Set up isExternalUser
     *
     * @param bool|true $isExternalUser
     *
     * @return @void
     */
    protected function setupIsExternalUser($isExternalUser = true)
    {
        $this->mockedSmServices[\ZfcRbac\Service\AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER, null)
            ->atLeast()->once()
            ->andReturn($isExternalUser);
    }

    /**
     * Get an Application Entity to be used in tests
     *
     * @param null|Licence        $licence     If null a new Licence is created
     * @param null|string|RefData $status      If null application status is set to VALID
     * @param null|0|1            $isVariation If null set to 0 (new application)
     *
     * @return Application
     */
    protected function getTestingApplication(Licence $licence = null, $status = null, $isVariation = 0)
    {
        if ($licence === null) {
            $licence = $this->getTestingLicence();
        }

        if ($status === null) {
            $status = new RefData(Application::APPLICATION_STATUS_VALID);
        } elseif (is_string($status)) {
            $status = new RefData($status);
        }

        return new Application($licence, $status, $isVariation);
    }

    /**
     * Get an Licence Entity to be used in tests
     *
     * @param null|Organisation   $organisation If null a new Organisation is created
     * @param null|string|RefData $status       If null licence status is set to VALID
     *
     * @return Licence
     */
    protected function getTestingLicence(
        Organisation $organisation = null,
        $status = null
    ) {
        if ($organisation === null) {
            $organisation = new Organisation();
        }

        if ($status === null) {
            $status = new RefData(Licence::LICENCE_STATUS_VALID);
        } elseif (is_string($status)) {
            $status = new RefData($status);
        }

        $licence = new Licence($organisation, $status);

        $ta = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $ta->setId('T');
        $licence->setTrafficArea($ta);

        return $licence;
    }
}
