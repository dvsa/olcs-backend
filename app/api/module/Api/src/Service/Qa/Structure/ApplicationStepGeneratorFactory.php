<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApplicationStepGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationStepGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationStepGenerator(
            $serviceLocator->get('QaFormControlStrategyProvider'),
            $serviceLocator->get('QaApplicationStepFactory'),
            $serviceLocator->get('QaValidatorListGenerator'),
            $serviceLocator->get('QaElementGeneratorContextFactory')
        );
    }
}
