<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
