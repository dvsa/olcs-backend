<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SelfservePageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SelfservePageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SelfservePageGenerator(
            $serviceLocator->get('QaSelfservePageFactory'),
            $serviceLocator->get('QaApplicationStepGenerator'),
            $serviceLocator->get('FormControlServiceManager'),
            $serviceLocator->get('QaContextFactory')
        );
    }
}
