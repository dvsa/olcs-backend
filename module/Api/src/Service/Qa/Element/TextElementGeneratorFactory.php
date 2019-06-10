<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TextElementGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TextElementGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TextElementGenerator(
            $serviceLocator->get('QaTextElementFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
