<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
