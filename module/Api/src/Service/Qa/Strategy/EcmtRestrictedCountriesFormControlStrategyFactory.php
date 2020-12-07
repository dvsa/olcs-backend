<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
