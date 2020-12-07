<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HeaderAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return HeaderAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new HeaderAnswersSummaryRowsAdder(
            $serviceLocator->get('PermitsAnswersSummaryRowFactory'),
            $serviceLocator->get('ViewRenderer')
        );
    }
}
