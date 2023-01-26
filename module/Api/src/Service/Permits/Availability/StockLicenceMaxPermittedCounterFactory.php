<?php

namespace Dvsa\Olcs\Api\Service\Permits\Availability;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StockLicenceMaxPermittedCounterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockLicenceMaxPermittedCounter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StockLicenceMaxPermittedCounter
    {
        return $this->__invoke($serviceLocator, StockLicenceMaxPermittedCounter::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StockLicenceMaxPermittedCounter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StockLicenceMaxPermittedCounter
    {
        return new StockLicenceMaxPermittedCounter(
            $container->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
