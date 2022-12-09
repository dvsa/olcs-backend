<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationUpdater
    {
        return $this->__invoke($serviceLocator, ApplicationUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationUpdater
     */
public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationUpdater
    {
        return new ApplicationUpdater(
            $container->get('PermitsBilateralInternalApplicationCountryUpdater')
        );
    }
}
