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
        return new BaseFormControlStrategy(
            'ecmt_st_restricted_countries',
            $serviceLocator->get('QaEcmtShortTermRestrictedCountriesElementGenerator'),
            $serviceLocator->get('QaEcmtShortTermRestrictedCountriesAnswerSaver'),
            $serviceLocator->get('QaEcmtShortTermRestrictedCountriesAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator')
        );
    }
}
