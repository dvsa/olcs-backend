<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        return new BaseFormControlStrategy(
            'bilateral_number_of_permits',
            $mainServiceLocator->get('QaBilateralNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerSummaryProvider')
        );
    }
}
