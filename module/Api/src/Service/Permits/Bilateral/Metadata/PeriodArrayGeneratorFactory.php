<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PeriodArrayGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PeriodArrayGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PeriodArrayGenerator(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $serviceLocator->get('PermitsBilateralMetadataPeriodGenerator'),
            $serviceLocator->get('CommonCurrentDateTimeFactory')
        );
    }
}
