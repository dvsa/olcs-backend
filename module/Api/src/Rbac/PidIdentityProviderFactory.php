<?php

namespace Dvsa\Olcs\Api\Rbac;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PidIdentityProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new PidIdentityProvider(
            $serviceLocator->get('RepositoryServiceManager')->get('User'),
            $serviceLocator->get('Request'),
            isset($config['openam']['pid_header']) ? $config['openam']['pid_header'] : 'X-Pid'
        );
    }
}