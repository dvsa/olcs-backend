<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckboxGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckboxGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckboxGenerator(
            $serviceLocator->get('QaCheckboxElementFactory'),
            $serviceLocator->get('QaTranslateableTextGenerator')
        );
    }
}
