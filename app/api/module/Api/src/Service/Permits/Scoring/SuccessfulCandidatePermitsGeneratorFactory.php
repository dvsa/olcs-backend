<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
