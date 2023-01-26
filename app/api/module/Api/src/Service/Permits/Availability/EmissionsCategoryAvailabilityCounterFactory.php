<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EmissionsCategoryAvailabilityCounterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryAvailabilityCounter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EmissionsCategoryAvailabilityCounter
    {
        return $this->__invoke($serviceLocator, EmissionsCategoryAvailabilityCounter::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EmissionsCategoryAvailabilityCounter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EmissionsCategoryAvailabilityCounter
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new EmissionsCategoryAvailabilityCounter(
            $container->get('doctrine.connection.ormdefault'),
            $repoServiceManager->get('IrhpPermitRange'),
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('IrhpPermit'),
            $repoServiceManager->get('IrhpPermitStock'),
            $repoServiceManager->get('IrhpCandidatePermit')
        );
    }
}
