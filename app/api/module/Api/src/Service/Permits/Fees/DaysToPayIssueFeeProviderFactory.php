<?php

namespace Dvsa\Olcs\Api\Service\Permits\Fees;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class DaysToPayIssueFeeProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DaysToPayIssueFeeProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DaysToPayIssueFeeProvider
    {
        return $this->__invoke($serviceLocator, DaysToPayIssueFeeProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DaysToPayIssueFeeProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DaysToPayIssueFeeProvider
    {
        return new DaysToPayIssueFeeProvider(
            $container->get('RepositoryServiceManager')->get('SystemParameter')
        );
    }
}
