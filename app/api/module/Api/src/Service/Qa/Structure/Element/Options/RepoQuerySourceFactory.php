<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepoQuerySourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RepoQuerySource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RepoQuerySource(
            $serviceLocator->get('RepositoryServiceManager')
        );
    }
}
