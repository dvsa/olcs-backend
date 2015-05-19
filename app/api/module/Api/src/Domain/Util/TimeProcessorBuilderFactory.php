<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TimeProcessorBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TimeProcessorBuilder($serviceLocator->get('RepositoryServiceManager')->get('PublicHoliday'));
    }
}