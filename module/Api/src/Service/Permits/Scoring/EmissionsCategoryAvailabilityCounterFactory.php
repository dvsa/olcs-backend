<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EmissionsCategoryAvailabilityCounterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryAvailabilityCounter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new EmissionsCategoryAvailabilityCounter(
            $repoServiceManager->get('IrhpPermitRange'),
            $repoServiceManager->get('IrhpPermit'),
            $repoServiceManager->get('IrhpApplication')
        );
    }
}
