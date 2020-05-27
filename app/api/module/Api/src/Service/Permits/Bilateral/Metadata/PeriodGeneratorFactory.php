<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PeriodGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PeriodGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PeriodGenerator(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $serviceLocator->get('PermitsBilateralMetadataFieldsGenerator')
        );
    }
}
