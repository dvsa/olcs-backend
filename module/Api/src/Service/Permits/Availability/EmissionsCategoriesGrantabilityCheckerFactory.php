<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EmissionsCategoriesGrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoriesGrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EmissionsCategoriesGrantabilityChecker
    {
        return $this->__invoke($serviceLocator, EmissionsCategoriesGrantabilityChecker::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EmissionsCategoriesGrantabilityChecker
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EmissionsCategoriesGrantabilityChecker
    {
        return new EmissionsCategoriesGrantabilityChecker(
            $container->get('PermitsAvailabilityEmissionsCategoryAvailabilityCounter')
        );
    }
}
