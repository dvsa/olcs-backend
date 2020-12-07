<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsGenerator(
            $serviceLocator->get('QaBilateralNoOfPermitsElementFactory'),
            $serviceLocator->get('QaBilateralNoOfPermitsTextFactory')
        );
    }
}
