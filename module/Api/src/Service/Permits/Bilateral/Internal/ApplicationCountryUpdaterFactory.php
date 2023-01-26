<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationCountryUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationCountryUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationCountryUpdater
    {
        return $this->__invoke($serviceLocator, ApplicationCountryUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationCountryUpdater
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationCountryUpdater
    {
        return new ApplicationCountryUpdater(
            $container->get('PermitsBilateralInternalIrhpPermitApplicationCreator'),
            $container->get('PermitsBilateralInternalExistingIrhpPermitApplicationHandler')
        );
    }
}
