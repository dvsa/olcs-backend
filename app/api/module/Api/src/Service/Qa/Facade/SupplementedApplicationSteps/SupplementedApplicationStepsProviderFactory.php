<?php

namespace Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('QaFormControlStrategyProvider'),
            $serviceLocator->get('QaSupplementedApplicationStepFactory')
        );
    }
}
