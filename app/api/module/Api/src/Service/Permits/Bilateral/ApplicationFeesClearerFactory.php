<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationFeesClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationFeesClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationFeesClearer(
            $serviceLocator->get('CqrsCommandCreator'),
            $serviceLocator->get('CommandHandlerManager'),
            $serviceLocator->get('RepositoryServiceManager')->get('Fee')
        );
    }
}
