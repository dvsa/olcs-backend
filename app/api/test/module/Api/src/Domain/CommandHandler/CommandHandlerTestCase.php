<?php

/**
 * Command Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Command Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class CommandHandlerTestCase extends MockeryTestCase
{
    /**
     * @var CommandHandlerInterface
     */
    protected $sut;

    /**
     * @var CommandHandlerManager
     */
    protected $commandHandler;

    /**
     * @var ServiceLocatorInterface
     */
    protected $repoManager;

    protected $repoMap = [];

    protected $sideEffects = [];

    protected $commands = [];

    protected $refData = [];

    protected $references = [];

    protected $categoryReferences = [];

    protected $subCategoryReferences = [];

    private $initRefdata = false;

    protected $mockedSmServices = [];

    protected $queryHandler;

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn(m::mock(TransactionManagerInterface::class));
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
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
        $this->repoMap[$name] = m::mock($class);
        $this->repoMap[$name]->shouldReceive('getRefdataReference')
            ->andReturnUsing([$this, 'mapRefData'])
            ->shouldReceive('getReference')
            ->andReturnUsing([$this, 'mapReference'])
            ->shouldReceive('getCategoryReference')
            ->andReturnUsing([$this, 'mapCategoryReference'])
            ->shouldReceive('getSubCategoryReference')
            ->andReturnUsing([$this, 'mapSubCategoryReference']);
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
                    $mock->makePartial();
                    $mock->setId($id);
                }
            }

            $this->initRefdata = true;
        }
    }

    public function tearDown()
    {
        $this->initRefdata = false;
        $this->assertCommandData();

        parent::tearDown();

        unset($this->sut);
        unset($this->commandHandler);
        unset($this->repoManager);
        unset($this->repoMap);
        unset($this->sideEffects);
        unset($this->commands);
        unset($this->refData);
        unset($this->references);
        unset($this->categoryReferences);
        unset($this->subCategoryReferences);
        unset($this->initRefdata);
        unset($this->mockedSmServices);
    }

    public function expectedSideEffect($class, $data, $result)
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type($class))
            ->andReturnUsing(
                function (CommandInterface $command) use ($class, $data, $result) {
                    $this->commands[] = [$command, $data];
                    return $result;
                }
            );
    }

    public function expectedSideEffectThrowsException($class, $data, $exception)
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->with(m::type($class))
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
            ->once()->atLeast()
            ->andReturn($isInternalUser);
    }
}
