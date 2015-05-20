<?php

/**
 * Command Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Command Handler Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerTestCase extends MockeryTestCase
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

    private $initRefdata = false;

    public function setUp()
    {
        $this->repoManager = m::mock(RepositoryServiceManager::class)->makePartial();

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager->setService($alias, $service);
        }

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->setService('RepositoryServiceManager', $this->repoManager);

        $this->commandHandler = m::mock(CommandHandlerManager::class)->makePartial();
        $this->commandHandler->setServiceLocator($sm);

        $this->sut = $this->sut->createService($this->commandHandler);

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
            ->andReturnUsing([$this, 'mapReference']);
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

            foreach ($this->references as $type => $mocks) {
                foreach ($mocks as  $id => $mock) {
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

    public function mapRefData($key)
    {
        return isset($this->refData[$key]) ? $this->refData[$key] : null;
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

            $this->assertEquals($data, $cmd->getArrayCopy(), get_class($cmd) . ' has unexpected data');
        }
    }
}
