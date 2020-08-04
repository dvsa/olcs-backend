<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtRestrictedCountriesFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_st_restricted_countries',
            $mainServiceLocator->get('QaEcmtRestrictedCountriesElementGenerator'),
            $mainServiceLocator->get('QaEcmtRestrictedCountriesAnswerSaver'),
            $mainServiceLocator->get('QaEcmtRestrictedCountriesAnswerClearer'),
            $mainServiceLocator->get('QaEcmtRestrictedCountriesQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtRestrictedCountriesAnswerSummaryProvider')
        );
    }
}
