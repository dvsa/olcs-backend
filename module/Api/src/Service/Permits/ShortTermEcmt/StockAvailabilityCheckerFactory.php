<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StockAvailabilityCheckerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StockAvailabilityChecker
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StockAvailabilityChecker(
            $serviceLocator->get('PermitsShortTermEcmtEmissionsCategoryAvailabilityChecker')
        );
    }
}
