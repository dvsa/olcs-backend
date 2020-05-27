<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CountryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CountryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CountryGenerator(
            $serviceLocator->get('PermitsBilateralMetadataPeriodArrayGenerator')
        );
    }
}
