<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckboxElementGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckboxElementGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckboxElementGenerator(
            $serviceLocator->get('QaCheckboxElementFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
