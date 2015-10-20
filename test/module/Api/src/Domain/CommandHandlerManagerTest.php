<?php

/**
 * Command Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Command Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandHandlerManagerTest extends MockeryTestCase
{
    /**
     * @var CommandHandlerManager
     */
    private $sut;

    public function setUp()
    {
        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(CommandHandlerManager::class));

        $this->sut = new CommandHandlerManager($config);
    }

    public function testHandleCommand()
    {
        $command = m::mock(CommandInterface::class)->makePartial();
        $command->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(CommandHandlerInterface::class);
        $mockService->shouldReceive('handleCommand')->with($command)->andReturn(['response']);

        $this->sut->setService(get_class($command), $mockService);

        $this->assertEquals(['response'], $this->sut->handleCommand($command));
    }

    public function testHandleCommandInvalid()
    {
        $this->setExpectedException(RuntimeException::class);

        $command = m::mock(CommandInterface::class)->makePartial();

        $mockService = m::mock();
        $mockService->shouldReceive('handleCommand')->with($command)->andReturn(['response']);

        $this->sut->setService(get_class($command), $mockService);

        $this->sut->handleCommand($command);
    }
}
