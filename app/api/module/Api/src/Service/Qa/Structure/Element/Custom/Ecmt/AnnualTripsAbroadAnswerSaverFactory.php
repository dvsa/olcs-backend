<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AnnualTripsAbroadAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnnualTripsAbroadAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AnnualTripsAbroadAnswerSaver(
            $serviceLocator->get('QaBaseAnswerSaver')
        );
    }
}
