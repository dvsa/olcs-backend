<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RestrictedCountriesGenerator(
            $serviceLocator->get('QaEcmtRestrictedCountriesElementFactory'),
            $serviceLocator->get('QaEcmtRestrictedCountryFactory'),
            $serviceLocator->get('RepositoryServiceManager')->get('Country'),
            $serviceLocator->get('QaGenericAnswerProvider'),
            $serviceLocator->get('PermitsCommonStockBasedPermitTypeConfigProvider')
        );
    }
}
