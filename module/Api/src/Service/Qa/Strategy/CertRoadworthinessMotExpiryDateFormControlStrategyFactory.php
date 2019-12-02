<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CertRoadworthinessMotExpiryDateFormControlStrategyFactory implements FactoryInterface
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
            'cert_road_mot_expiry_date',
            $serviceLocator->get('QaCertRoadworthinessMotExpiryDateElementGenerator'),
            $serviceLocator->get('QaDateAnswerSaver'),
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator')
        );
    }
}
