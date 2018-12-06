<?php

/**
 * Command Handler Manager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Dvsa\Olcs\Api\Domain\ValidationHandlerManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

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

    private $vhm;

    public function setUp()
    {
        $this->vhm = m::mock(ValidationHandlerManager::class)->makePartial();

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->setService('ValidationHandlerManager', $this->vhm);

        $config = m::mock(ConfigInterface::class);
        $config->shouldReceive('configureServiceManager')->with(m::type(CommandHandlerManager::class));

        $this->sut = new CommandHandlerManager($config);
        $this->sut->setServiceLocator($sm);
    }

    public function testHandleCommand()
    {
        $command = m::mock(CommandInterface::class)->makePartial();
        $command->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(CommandHandlerInterface::class);
        $mockService->shouldReceive('handleCommand')->with($command)->andReturn(['response']);
        $mockService->shouldReceive('checkEnabled')->once()->andReturn(true);

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($command)->andReturn(true);
        $this->vhm->setService(get_class($mockService), $mockValidator);

        $this->sut->setService(get_class($command), $mockService);

        $this->assertEquals(['response'], $this->sut->handleCommand($command));
    }

    public function testHandleCommandWithWrapped()
    {
        $command = m::mock(CommandInterface::class)->makePartial();
        $command->shouldReceive('getArrayCopy')->once()->andReturn(['foo' => 'bar']);

        $wrapped = m::mock(CommandHandlerInterface::class);
        $wrapped->shouldReceive('checkEnabled')->once()->andReturn(true);

        $mockService = m::mock(TransactioningCommandHandler::class);
        $mockService->shouldReceive('getWrapped')->andReturn($wrapped);
        $mockService->shouldReceive('handleCommand')->with($command)->andReturn(['response']);

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($command)->andReturn(true);
        $this->vhm->setService(get_class($wrapped), $mockValidator);

        $this->sut->setService(get_class($command), $mockService);

        $this->assertEquals(['response'], $this->sut->handleCommand($command));
    }

    public function testHandleCommandFailedValidator()
    {
        $this->setExpectedException(ForbiddenException::class);

        $command = m::mock(CommandInterface::class)->makePartial();
        $command->shouldReceive('getArrayCopy')->twice()->andReturn(['foo' => 'bar']);

        $mockService = m::mock(CommandHandlerInterface::class);
        $mockService->shouldReceive('handleCommand')->never();
        $mockService->shouldReceive('checkEnabled')->once()->andReturn(true);

        $mockValidator = m::mock(HandlerInterface::class);
        $mockValidator->shouldReceive('isValid')->with($command)->andReturn(false);
        $this->vhm->setService(get_class($mockService), $mockValidator);

        $this->sut->setService(get_class($command), $mockService);

        $this->sut->handleCommand($command);
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
