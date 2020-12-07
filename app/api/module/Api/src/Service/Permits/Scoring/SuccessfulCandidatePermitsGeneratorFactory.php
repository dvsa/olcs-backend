<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SuccessfulCandidatePermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SuccessfulCandidatePermitsGenerator(
            $serviceLocator->get('PermitsScoringEmissionsCategoryAvailabilityCounter')
        );
    }
}
