<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EcmtAnnualTripsAbroadFormControlStrategyFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BaseFormControlStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        return new BaseFormControlStrategy(
            'ecmt_st_annual_trips_abroad',
            $mainServiceLocator->get('QaEcmtAnnualTripsAbroadElementGenerator'),
            $mainServiceLocator->get('QaEcmtAnnualTripsAbroadAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaGenericAnswerSummaryProvider')
        );
    }
}
