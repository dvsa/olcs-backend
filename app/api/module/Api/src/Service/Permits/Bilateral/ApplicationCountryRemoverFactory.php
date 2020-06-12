<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApplicationCountryRemoverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationCountryRemover
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationCountryRemover(
            $serviceLocator->get('CqrsCommandCreator'),
            $serviceLocator->get('CommandHandlerManager')
        );
    }
}
