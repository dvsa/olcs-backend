<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class BilateralStandardAndCabotageFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_standard_and_cabotage',
            $mainServiceLocator->get('QaBilateralStandardAndCabotageElementGenerator'),
            $mainServiceLocator->get('QaBilateralStandardAndCabotageAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaBilateralCabotageQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralStandardAndCabotageAnswerSummaryProvider')
        );
    }
}
