<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Faker\Factory;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new User(
            $container->get(ClientInterface::class),
            Factory::create()
        );
    }
}
