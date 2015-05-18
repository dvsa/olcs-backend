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
    }

    public function expectedSideEffect($class, $data, $result)
    {
        $this->commandHandler->shouldReceive('handleCommand')
            ->andReturnUsing(
                function (CommandInterface $command) use ($class, $data, $result) {
                    $this->assertInstanceOf($class, $command);
                    $this->assertEquals($data, $command->getArrayCopy());

                    return $result;
                }
            );
    }
}
