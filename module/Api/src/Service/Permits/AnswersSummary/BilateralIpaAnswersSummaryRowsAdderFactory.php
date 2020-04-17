<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralIpaAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralIpaAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new BilateralIpaAnswersSummaryRowsAdder(
            $serviceLocator->get('PermitsAnswersSummaryRowFactory'),
            $serviceLocator->get('ViewRenderer'),
            $serviceLocator->get('QaAnswersSummaryRowsAdder'),
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock')
        );
    }
}
