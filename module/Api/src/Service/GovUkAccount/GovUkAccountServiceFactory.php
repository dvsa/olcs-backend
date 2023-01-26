<?php

namespace Dvsa\Olcs\Api\Service\GovUkAccount;

use Dvsa\GovUkAccount\Provider\GovUkAccount;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GovUkAccountServiceFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GovUkAccountService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GovUkAccountService
    {
        $config = $container->get('Config')['govuk_account'];

        return new GovUkAccountService($config, new GovUkAccount($config));
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GovUkAccountService
    {
        return $this($serviceLocator, GovUkAccountService::class);
    }
}
