<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DatabaseTwigLoaderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DatabaseTwigLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DatabaseTwigLoader(
            $serviceLocator->get('TemplateDatabaseTemplateFetcher')
        );
    }
}
