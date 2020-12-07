<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpCandidatePermit'),
            $serviceLocator->get('PermitsAllocateEmissionsStandardCriteriaFactory')
        );
    }
}
