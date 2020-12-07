<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SupplementedApplicationStepsProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SupplementedApplicationStepsProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SupplementedApplicationStepsProvider(
            $serviceLocator->get('FormControlServiceManager'),
            $serviceLocator->get('QaSupplementedApplicationStepFactory')
        );
    }
}
