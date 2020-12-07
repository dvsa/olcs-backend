<?php

/**
 * Pid Identity Provider Factory
 */
namespace Dvsa\Olcs\Api\Rbac;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Pid Identity Provider Factory
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
