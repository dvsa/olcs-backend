<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatabaseTemplateFetcherFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DatabaseTemplateFetcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DatabaseTemplateFetcher(
            $serviceLocator->get('RepositoryServiceManager')->get('Template')
        );
    }
}
