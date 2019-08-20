<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AnswersSummaryRowsAdder(
            $serviceLocator->get('QaSupplementedApplicationStepsProvider'),
            $serviceLocator->get('QaAnswersSummaryRowGenerator')
        );
    }
}
