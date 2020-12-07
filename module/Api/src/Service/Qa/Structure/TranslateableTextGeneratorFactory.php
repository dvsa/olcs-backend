<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TranslateableTextGenerator(
            $serviceLocator->get('QaTranslateableTextFactory'),
            $serviceLocator->get('QaTranslateableTextParameterGenerator')
        );
    }
}
