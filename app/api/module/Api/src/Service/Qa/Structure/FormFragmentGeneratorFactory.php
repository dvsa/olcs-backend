<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaApplicationStepGenerator'),
            $serviceLocator->get('QaContextFactory')
        );
    }
}
