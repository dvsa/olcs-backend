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
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        return new BaseFormControlStrategy(
            'bilateral_permit_usage',
            $mainServiceLocator->get('QaBilateralPermitUsageGenerator'),
            $mainServiceLocator->get('QaBilateralPermitUsageAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaBilateralPermitUsageQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralPermitUsageAnswerSummaryProvider')
        );
    }
}
