<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslateableTextParameterGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextParameterGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TranslateableTextParameterGenerator(
            $serviceLocator->get('QaTranslateableTextParameterFactory')
        );
    }
}
