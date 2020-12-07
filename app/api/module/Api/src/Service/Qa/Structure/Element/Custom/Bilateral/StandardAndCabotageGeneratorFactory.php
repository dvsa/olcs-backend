<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageGenerator(
            $serviceLocator->get('QaBilateralStandardAndCabotageElementFactory')
        );
    }
}
