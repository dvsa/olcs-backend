<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AbstractEcmtNoOfPermitsFormControlStrategyFactory implements FactoryInterface
{
    protected $frontendComponent = 'changeMe';

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
            $this->frontendComponent,
            $mainServiceLocator->get('QaEcmtNoOfPermitsElementGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaQuestionTextGenerator'),
            $mainServiceLocator->get('QaEcmtNoOfPermitsAnswerSummaryProvider')
        );
    }
}
