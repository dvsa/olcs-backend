<?php

namespace Dvsa\Olcs\Api\Service\Permits\CandidatePermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApggCandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApggCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApggCandidatePermitsCreator(
            $serviceLocator->get('PermitsCandidatePermitsApggEmissionsCatCandidatePermitsCreator')
        );
    }
}
