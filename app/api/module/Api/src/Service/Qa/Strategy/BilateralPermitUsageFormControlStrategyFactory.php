<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralPermitUsageFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_permit_usage',
            $serviceLocator->get('QaBilateralPermitUsageGenerator'),
            $serviceLocator->get('QaGenericAnswerSaver'),
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('QaBilateralPermitUsageQuestionTextGenerator'),
            $serviceLocator->get('QaBilateralPermitUsageAnswerSummaryProvider')
        );
    }
}
