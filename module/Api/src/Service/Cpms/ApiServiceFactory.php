<?php

namespace Dvsa\Olcs\Api\Service\Cpms;

use Dvsa\Olcs\Cpms\Service\ApiServiceFactory as CpmsApiService;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class ApiServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $authService = $serviceLocator->get(AuthorizationService::class);
        $userId = $authService->getIdentity()->getUser()->getId();

        $apiService = new CpmsApiService($config, $userId);
        return $apiService->createApiService();
    }
}
