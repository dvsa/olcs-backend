<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CheckedValueUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckedValueUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckedValueUpdater(
            $serviceLocator->get('RepositoryServiceManager')->get('Task')
        );
    }
}
