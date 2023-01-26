<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

abstract class AbstractEcmtPermitUsageRefDataSourceFactory implements FactoryInterface
{
    // override in inheriting classes
    const TRANSFORMATIONS = [];

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EcmtPermitUsageRefDataSource
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EcmtPermitUsageRefDataSource
    {
        return $this->__invoke($serviceLocator, EcmtPermitUsageRefDataSource::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EcmtPermitUsageRefDataSource
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EcmtPermitUsageRefDataSource
    {
        return new EcmtPermitUsageRefDataSource(
            $container->get('QaRefDataOptionsSource'),
            static::TRANSFORMATIONS
        );
    }
}
