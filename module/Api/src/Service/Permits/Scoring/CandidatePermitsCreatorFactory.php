<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CandidatePermitsCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new CandidatePermitsCreator(
            $repoServiceManager->get('IrhpCandidatePermit'),
            $repoServiceManager->get('SystemParameter'),
            $serviceLocator->get('PermitsScoringIrhpCandidatePermitFactory')
        );
    }
}
