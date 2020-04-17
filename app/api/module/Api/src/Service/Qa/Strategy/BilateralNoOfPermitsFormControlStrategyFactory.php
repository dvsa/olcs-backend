<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralNoOfPermitsFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_number_of_permits',
            $serviceLocator->get('QaBilateralNoOfPermitsElementGenerator'),
            $serviceLocator->get('QaBilateralNoOfPermitsAnswerSaver'),
            $serviceLocator->get('QaBilateralNoOfPermitsAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator'),
            $serviceLocator->get('QaBilateralNoOfPermitsAnswerSummaryProvider')
        );
    }
}
