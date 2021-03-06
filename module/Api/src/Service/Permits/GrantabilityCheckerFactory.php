<?php

namespace Dvsa\Olcs\Api\Service\Permits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GrantabilityChecker(
            $serviceLocator->get('PermitsAvailabilityEmissionsCategoriesGrantabilityChecker'),
            $serviceLocator->get('PermitsAvailabilityCandidatePermitsGrantabilityChecker')
        );
    }
}
