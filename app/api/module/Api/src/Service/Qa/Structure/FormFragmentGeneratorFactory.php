<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormFragmentGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormFragmentGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FormFragmentGenerator(
            $serviceLocator->get('QaFormFragmentFactory'),
            $serviceLocator->get('QaApplicationStepGenerator')
        );
    }
}
