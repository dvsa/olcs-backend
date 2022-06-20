<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractGeneratorServicesFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AbstractGeneratorServices(
            $container->get('ViewRenderer')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractGeneratorServices
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, AbstractGeneratorServices::class);
    }
}
