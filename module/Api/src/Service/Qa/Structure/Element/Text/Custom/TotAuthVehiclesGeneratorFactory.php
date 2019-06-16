<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TotAuthVehiclesGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TotAuthVehiclesGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TotAuthVehiclesGenerator(
            $serviceLocator->get('QaTextElementGenerator')
        );
    }
}
