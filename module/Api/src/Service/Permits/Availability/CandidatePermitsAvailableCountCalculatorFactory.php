<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CandidatePermitsAvailableCountCalculatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsAvailableCountCalculator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new CandidatePermitsAvailableCountCalculator(
            $repoServiceManager->get('IrhpCandidatePermit'),
            $repoServiceManager->get('IrhpPermit')
        );
    }
}
