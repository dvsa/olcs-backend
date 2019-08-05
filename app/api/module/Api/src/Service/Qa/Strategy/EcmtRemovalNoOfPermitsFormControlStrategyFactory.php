<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return new BaseFormControlStrategy(
            'text',
            $serviceLocator->get('QaTotAuthVehiclesTextElementGenerator'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsAnswerSaver'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsAnswerClearer'),
            $serviceLocator->get('QaEcmtRemovalNoOfPermitsQuestionTextGenerator')
        );
    }
}
