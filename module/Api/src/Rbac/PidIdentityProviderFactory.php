<?php

namespace Dvsa\Olcs\Api\Rbac;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see PidIdentityProvider
 */
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
