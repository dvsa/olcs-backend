<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('doctrine.connection.ormdefault'),
            $repoServiceManager->get('IrhpPermitRange'),
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('IrhpPermit'),
            $repoServiceManager->get('IrhpPermitStock'),
            $repoServiceManager->get('IrhpCandidatePermit')
        );
    }
}
