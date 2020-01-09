<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtShortTermSectorsFormControlStrategyFactory implements FactoryInterface
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
            'radio',
            $serviceLocator->get('QaRadioElementGenerator'),
            $serviceLocator->get('QaEcmtShortTermSectorsAnswerSaver'),
            $serviceLocator->get('QaEcmtShortTermSectorsAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator'),
            $serviceLocator->get('QaRadioAnswerSummaryProvider')
        );
    }
}
