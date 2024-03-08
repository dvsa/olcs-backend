<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Faker\Factory;
use Psr\Container\ContainerInterface;

/**
 * User Factory
 */
class UserFactory implements FactoryInterface
{
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
