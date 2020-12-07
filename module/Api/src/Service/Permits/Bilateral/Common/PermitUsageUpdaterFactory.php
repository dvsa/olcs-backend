<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PermitUsageUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageUpdater(
            $serviceLocator->get('PermitsBilateralCommonModifiedAnswerUpdater')
        );
    }
}
