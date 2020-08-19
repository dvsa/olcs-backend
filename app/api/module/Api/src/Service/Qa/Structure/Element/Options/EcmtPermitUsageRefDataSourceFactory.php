<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtPermitUsageRefDataSourceFactory implements FactoryInterface
{
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
            $serviceLocator->get('QaRefDataOptionsSource')
        );
    }
}
