<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Logger\EntityAccessLogger;

class QueryHandlerManagerMockBuilder
{
    /**
     * @param ServiceManager $serviceManager
     * @return QueryHandlerManager|m\LegacyMockInterface|m\MockInterface
     */
    public function build(ServiceManager $serviceManager)
    {
        $queryManager = m::mock(QueryHandlerManager::class);
        $queryManager->shouldReceive('getServiceLocator')->andReturn($serviceManager)->byDefault();
        $queryManager->shouldReceive('handleQuery')->andReturn([])->byDefault();
        return $queryManager;
    }

    /**
     * @param QueryHandlerManager $queryManager
     */
    public function register(QueryHandlerManager $queryManager)
    {
        $serviceManager = $queryManager->getServiceLocator();

        if (! $serviceManager->has(EntityAccessLogger::class)) {
            $instance = m::mock(EntityAccessLogger::class)->shouldIgnoreMissing();
            $serviceManager->setService(EntityAccessLogger::class, $instance);
        }

        $serviceManager->setService(QueryHandlerManager::class, $queryManager);
    }
}
