<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
