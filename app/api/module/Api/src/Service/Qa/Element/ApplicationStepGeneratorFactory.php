<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

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
            $serviceLocator->get('RepositoryServiceManager')->get('Answer'),
            $serviceLocator->get('QaValidatorListGenerator')
        );
    }
}
