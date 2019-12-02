<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
