<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class MultilateralAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return MultilateralAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MultilateralAnswersSummaryRowsAdder(
            $serviceLocator->get('PermitsAnswersSummaryRowFactory'),
            $serviceLocator->get('ViewRenderer')
        );
    }
}
