<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationAnswersClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationAnswersClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationAnswersClearer(
            $serviceLocator->get('QaSupplementedApplicationStepsProvider'),
            $serviceLocator->get('QaContextFactory')
        );
    }
}
