<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class BilateralAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new BilateralAnswersSummaryRowsAdder(
            $serviceLocator->get('PermitsAnswersSummaryRowFactory'),
            $serviceLocator->get('ViewRenderer'),
            $serviceLocator->get('PermitsBilateralIpaAnswersSummaryRowsAdder')
        );
    }
}
