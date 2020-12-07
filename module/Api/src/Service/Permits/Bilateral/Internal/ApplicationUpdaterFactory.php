<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationUpdater(
            $serviceLocator->get('PermitsBilateralInternalApplicationCountryUpdater')
        );
    }
}
