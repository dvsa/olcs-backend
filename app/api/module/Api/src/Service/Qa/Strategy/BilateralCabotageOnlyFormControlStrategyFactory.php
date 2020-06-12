<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return new BaseFormControlStrategy(
            'bilateral_cabotage_only',
            $serviceLocator->get('QaBilateralCabotageOnlyElementGenerator'),
            $serviceLocator->get('QaBilateralCabotageOnlyAnswerSaver'),
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('QaBilateralCabotageQuestionTextGenerator'),
            $serviceLocator->get('QaGenericAnswerSummaryProvider')
        );
    }
}
