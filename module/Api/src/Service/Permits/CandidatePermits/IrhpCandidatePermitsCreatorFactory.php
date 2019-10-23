<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IrhpCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IrhpCandidatePermitsCreator(
            $serviceLocator->get('PermitsScoringCandidatePermitsCreator'),
            $serviceLocator->get('PermitsCandidatePermitsApggCandidatePermitsCreator')
        );
    }
}
