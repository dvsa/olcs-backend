<?php

namespace Dvsa\OlcsTest;

use Dvsa\OlcsTest\Builder\ServiceManagerBuilder;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery\MockInterface;
use Mockery as m;

/**
 * @deprecated Please use \Olcs\TestHelpers\Service\MocksServicesTrait - this is shared between all of our projects
 */
trait MocksServicesTrait
{
    /**
     * @return ServiceLocatorInterface
     */
    protected function setUpServiceLocator(): ServiceLocatorInterface
    {
        return (new ServiceManagerBuilder(function (ServiceLocatorInterface $serviceLocator) {
            return $this->setUpDefaultServices($serviceLocator);
        }))->build();
    }

    /**
     * @param string $class
     * @return MockInterface
     */
    protected function setUpMockService(string $class): MockInterface
    {
        $instance = m::mock($class);
        $instance->shouldIgnoreMissing();
        return $instance;
    }

    /**
     * Sets up default services.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    abstract protected function setUpDefaultServices(ServiceLocatorInterface $serviceLocator): array;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @return MockInterface
     */
    protected function resolveMockService(ServiceLocatorInterface $serviceLocator, string $name): MockInterface
    {
        return $serviceLocator->get($name);
    }
}
