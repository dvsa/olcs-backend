<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageOnlyGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CabotageOnlyGenerator(
            $serviceLocator->get('QaBilateralCabotageOnlyElementFactory')
        );
    }
}
