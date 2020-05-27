<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
