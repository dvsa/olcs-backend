<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaTranslateableTextFactory')
        );
    }
}
