<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationCountryUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationCountryUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationCountryUpdater(
            $serviceLocator->get('PermitsBilateralInternalIrhpPermitApplicationCreator'),
            $serviceLocator->get('PermitsBilateralInternalExistingIrhpPermitApplicationHandler')
        );
    }
}
