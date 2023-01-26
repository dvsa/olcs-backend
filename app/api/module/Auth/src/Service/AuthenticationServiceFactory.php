<?php
declare(strict_types = 1);

namespace Dvsa\Olcs\Auth\Service;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Storage\NonPersistent;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthenticationServiceFactory
 * @see \AuthenticationServiceFactoryTest
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthenticationService
    {
        return new AuthenticationService(new NonPersistent());
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AuthenticationService
    {
        return $this->__invoke($serviceLocator, null);
    }
}
