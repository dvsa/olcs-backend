<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralNoOfPermitsMoroccoFormControlStrategyFactory implements FactoryInterface
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
            'bilateral_number_of_permits_morocco',
            $mainServiceLocator->get('QaBilateralNoOfPermitsMoroccoElementGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsMoroccoAnswerSaver'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaBilateralNoOfPermitsMoroccoAnswerSummaryProvider')
        );
    }
}
