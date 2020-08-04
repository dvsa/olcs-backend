<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtNoOfPermitsFormControlStrategyFactory implements FactoryInterface
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
            $mainServiceLocator->get('QaEcmtNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSummaryProvider')
        );
    }
}
