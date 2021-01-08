<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;

class QueryHandlerManagerMockBuilder implements BuilderInterface
{
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
        $queryManager = m::mock(QueryHandlerManager::class);
        $queryManager->shouldReceive('getServiceLocator')->andReturn($this->serviceLocator)->byDefault();
        $queryManager->shouldReceive('handleQuery')->andReturn([])->byDefault();
        return $queryManager;
    }
}
