<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtSectorsFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_sectors',
            $mainServiceLocator->get('QaRadioElementGenerator'),
            $mainServiceLocator->get('QaEcmtSectorsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtSectorsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaRadioAnswerSummaryProvider')
        );
    }
}
