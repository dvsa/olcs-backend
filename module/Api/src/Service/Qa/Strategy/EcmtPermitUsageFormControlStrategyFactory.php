<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EcmtPermitUsageFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_st_permit_usage',
            $mainServiceLocator->get('QaRadioElementGenerator'),
            $mainServiceLocator->get('QaGenericAnswerSaver'),
            $mainServiceLocator->get('QaGenericAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaRadioAnswerSummaryProvider')
        );
    }
}
