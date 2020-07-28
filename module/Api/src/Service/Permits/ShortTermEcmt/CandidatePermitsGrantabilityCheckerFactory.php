<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CandidatePermitsGrantabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsGrantabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CandidatePermitsGrantabilityChecker(
            $serviceLocator->get('PermitsShortTermEcmtCandidatePermitsAvailableCountCalculator')
        );
    }
}