<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FilteredTranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FilteredTranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FilteredTranslateableTextGenerator(
            $serviceLocator->get('QaFilteredTranslateableTextFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
