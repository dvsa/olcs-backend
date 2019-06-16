<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JsonDecodingFilteredTranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return JsonDecodingFilteredTranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new JsonDecodingFilteredTranslateableTextGenerator(
            $serviceLocator->get('QaFilteredTranslateableTextGenerator')
        );
    }
}
