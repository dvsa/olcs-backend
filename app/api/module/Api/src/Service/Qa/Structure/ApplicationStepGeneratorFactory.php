<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('FormControlServiceManager'),
            $serviceLocator->get('QaApplicationStepFactory'),
            $serviceLocator->get('QaElementGeneratorContextGenerator')
        );
    }
}
