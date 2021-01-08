<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;

class CommandHandlerManagerMockBuilder implements BuilderInterface
{
    const ALIAS = 'CommandHandlerManager';

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritDoc
     */
    public function build()
    {
        $manager = m::mock(CommandHandlerManager::class);
        $manager->shouldReceive('getServiceLocator')->andReturn($this->serviceLocator)->byDefault();
        $manager->shouldReceive('handleCommand')->andReturn(new Result())->byDefault();
        return $manager;
    }
}
