<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DeleteUserFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TransactioningCommandHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransactioningCommandHandler
    {
        assert($container instanceof ServiceLocatorAwareInterface);
        $pluginManager = $container;
        $container = $container->getServiceLocator();

        $adapter = $container->get(ValidatableAdapterInterface::class);

        $instance = new DeleteUser($adapter);
        return $instance->createService($pluginManager);
    }

    /**
     * @deprecated Remove following Laminas V3 upgrade
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactioningCommandHandler
    {
        return $this->__invoke($serviceLocator, DeleteUser::class);
    }
}
