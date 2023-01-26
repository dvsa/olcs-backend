<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Faker\Factory;
use Interop\Container\ContainerInterface;

/**
 * User Factory
 */
class UserFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator ZF Service locator
     *
     * @return User
     */
    public function createService(ServiceLocatorInterface $serviceLocator): User
    {
        return $this->__invoke($serviceLocator, User::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return User
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): User
    {
        return new User(
            $container->get(ClientInterface::class),
            Factory::create()
        );
    }
}
