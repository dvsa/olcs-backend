<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtShortTermRestrictedCountriesFormControlStrategyFactory implements FactoryInterface
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
            $mainServiceLocator->get('QaEcmtShortTermRestrictedCountriesElementGenerator'),
            $mainServiceLocator->get('QaEcmtShortTermRestrictedCountriesAnswerSaver'),
            $mainServiceLocator->get('QaEcmtShortTermRestrictedCountriesAnswerClearer'),
            $mainServiceLocator->get('QaEcmtShortTermRestrictedCountriesQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtShortTermRestrictedCountriesAnswerSummaryProvider')
        );
    }
}
