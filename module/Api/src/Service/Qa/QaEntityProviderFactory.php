<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class QaEntityProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QaEntityProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new QaEntityProvider(
            $repoServiceManager->get('IrhpApplication'),
            $repoServiceManager->get('IrhpPermitApplication')
        );
    }
}
