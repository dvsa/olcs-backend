<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtShortTermIntJourneysFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_st_international_journeys',
            $serviceLocator->get('QaEcmtShortTermIntJourneysElementGenerator'),
            $serviceLocator->get('QaEcmtShortTermIntJourneysAnswerSaver'),
            $serviceLocator->get('QaEcmtShortTermIntJourneysAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator'),
            $serviceLocator->get('QaRadioAnswerSummaryProvider')
        );
    }
}
