<?php

namespace Dvsa\Olcs\Api\Mvc;

use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class OlcsBlameableListenerFactory
 *
 * @package Olcs\Api\Mvc
 */
class OlcsBlameableListenerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OlcsBlameableListener
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OlcsBlameableListener
    {
        return new OlcsBlameableListener($container);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return OlcsBlameableListener
     */
    public function createService(ServiceLocatorInterface $services): OlcsBlameableListener
    {
        return $this($services, OlcsBlameableListener::class);
    }
}
