<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TimeProcessorBuilderFactory
 */
class TimeProcessorBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TimeProcessorBuilder($container->get('RepositoryServiceManager')->get('PublicHoliday'));
    }
}
