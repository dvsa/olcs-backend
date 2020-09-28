<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EcmtPermitUsageRefDataSource(
            $serviceLocator->get('QaRefDataOptionsSource'),
            static::TRANSFORMATIONS
        );
    }
}
