<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
