<?php

namespace Dvsa\Olcs\Api\Service\Qa\PostSubmit;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpApplicationPostSubmitHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpApplicationPostSubmitHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IrhpApplicationPostSubmitHandler(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
