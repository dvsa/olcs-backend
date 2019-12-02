<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommonCertificatesFormControlStrategyFactory implements FactoryInterface
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
            'checkbox',
            $serviceLocator->get('QaCheckboxElementGenerator'),
            $serviceLocator->get('QaCommonCertificatesAnswerSaver'),
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('QaCommonCertificatesQuestionTextGenerator')
        );
    }
}
