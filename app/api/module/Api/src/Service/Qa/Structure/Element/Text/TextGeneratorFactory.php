<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TextGenerator(
            $serviceLocator->get('QaTextElementFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
