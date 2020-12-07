<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EmissionsCategoryConditionalAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryConditionalAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EmissionsCategoryConditionalAdder(
            $serviceLocator->get('QaEcmtEmissionsCategoryFactory'),
            $serviceLocator->get('PermitsAvailabilityEmissionsCategoryAvailabilityCounter')
        );
    }
}
