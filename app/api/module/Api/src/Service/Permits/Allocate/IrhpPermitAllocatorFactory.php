<?php

namespace Dvsa\Olcs\Api\Service\Permits\Allocate;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpPermitAllocatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitAllocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IrhpPermitAllocator(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
