<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtShortTermNoOfPermitsFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_st_no_of_permits',
            $mainServiceLocator->get('QaEcmtShortTermNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaEcmtShortTermNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtShortTermNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaEcmtShortTermNoOfPermitsQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtShortTermNoOfPermitsAnswerSummaryProvider')
        );
    }
}
