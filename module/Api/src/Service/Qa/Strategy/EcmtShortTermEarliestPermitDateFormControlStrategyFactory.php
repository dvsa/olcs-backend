<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EcmtShortTermEarliestPermitDateFormControlStrategyFactory implements FactoryInterface
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
            'ecmt_st_earliest_permit_date',
            $serviceLocator->get('QaDateElementGenerator'),
            $serviceLocator->get('QaDateAnswerSaver'),
            $serviceLocator->get('QaGenericAnswerClearer'),
            $serviceLocator->get('QaQuestionTextGenerator')
        );
    }
}
