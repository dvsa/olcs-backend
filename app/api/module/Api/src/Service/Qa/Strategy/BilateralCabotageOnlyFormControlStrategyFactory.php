<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class BilateralCabotageOnlyFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_cabotage_only',
            $mainServiceLocator->get('QaBilateralCabotageOnlyElementGenerator'),
            $mainServiceLocator->get('QaBilateralCabotageOnlyAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaBilateralCabotageQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralCabotageOnlyAnswerSummaryProvider')
        );
    }
}
