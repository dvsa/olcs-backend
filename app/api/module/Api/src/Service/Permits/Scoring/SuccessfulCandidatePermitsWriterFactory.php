<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SuccessfulCandidatePermitsWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuccessfulCandidatePermitsWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SuccessfulCandidatePermitsWriter(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpCandidatePermit')
        );
    }
}
