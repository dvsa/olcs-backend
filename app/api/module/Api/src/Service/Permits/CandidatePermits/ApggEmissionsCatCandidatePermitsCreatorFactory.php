<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApggEmissionsCatCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApggEmissionsCatCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApggEmissionsCatCandidatePermitsCreator(
            $serviceLocator->get('PermitsCandidatePermitsApggCandidatePermitFactory'),
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpCandidatePermit')
        );
    }
}
