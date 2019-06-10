<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApplicationStepObjectsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationStepObjectsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new ApplicationStepObjectsProvider(
            $repoServiceManager->get('ApplicationStep'),
            $repoServiceManager->get('ApplicationPath'),
            $repoServiceManager->get('IrhpApplication')
        );
    }
}
