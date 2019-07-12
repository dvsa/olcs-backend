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
        return new BaseFormControlStrategy(
            'ecmt_st_no_of_permits',
            $serviceLocator->get('QaEcmtShortTermNoOfPermitsElementGenerator'),
            $serviceLocator->get('QaEcmtShortTermNoOfPermitsAnswerSaver'),
            $serviceLocator->get('QaEcmtShortTermNoOfPermitsQuestionTextGenerator')
        );
    }
}
