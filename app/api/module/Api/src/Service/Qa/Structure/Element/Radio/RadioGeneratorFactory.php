<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RadioGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RadioGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RadioGenerator(
            $serviceLocator->get('QaRadioElementFactory'),
            $serviceLocator->get('QaOptionListGenerator'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
