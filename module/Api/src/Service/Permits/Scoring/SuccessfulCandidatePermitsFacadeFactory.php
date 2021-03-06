<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SuccessfulCandidatePermitsFacadeFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsFacade
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SuccessfulCandidatePermitsFacade(
            $serviceLocator->get('PermitsScoringSuccessfulCandidatePermitsGenerator'),
            $serviceLocator->get('PermitsScoringSuccessfulCandidatePermitsWriter'),
            $serviceLocator->get('PermitsScoringSuccessfulCandidatePermitsLogger')
        );
    }
}
