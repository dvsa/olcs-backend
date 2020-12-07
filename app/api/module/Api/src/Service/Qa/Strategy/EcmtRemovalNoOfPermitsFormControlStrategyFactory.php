<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EcmtRemovalNoOfPermitsFormControlStrategyFactory implements FactoryInterface
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
            'text',
            $mainServiceLocator->get('QaTotAuthVehiclesTextElementGenerator'),
            $mainServiceLocator->get('QaEcmtRemovalNoOfPermitsAnswerSaver'),
            $mainServiceLocator->get('QaEcmtRemovalNoOfPermitsAnswerClearer'),
            $mainServiceLocator->get('QaEcmtRemovalNoOfPermitsQuestionTextGenerator'),
            $mainServiceLocator->get('QaGenericAnswerSummaryProvider')
        );
    }
}
