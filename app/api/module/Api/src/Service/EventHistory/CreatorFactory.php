<?php

namespace Dvsa\Olcs\Api\Service\EventHistory;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class CreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Creator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new Creator(
            $serviceLocator->get(AuthorizationService::class),
            $repoServiceManager->get('EventHistory'),
            $repoServiceManager->get('EventHistoryType')
        );
    }
}
