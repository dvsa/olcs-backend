<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaEcmtShortTermRestrictedCountriesElementFactory'),
            $serviceLocator->get('QaEcmtShortTermRestrictedCountryFactory'),
            $serviceLocator->get('RepositoryServiceManager')->get('Country'),
            $serviceLocator->get('QaGenericAnswerProvider')
        );
    }
}
