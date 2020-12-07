<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class TimeProcessorBuilderFactory
 */
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
