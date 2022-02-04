<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see ChangeExpiredPassword
 */
class ChangeExpiredPasswordFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ChangeExpiredPassword
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ChangeExpiredPassword
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $userRepository = $sl->get('RepositoryServiceManager')->get('User');
        return (new ChangeExpiredPassword($adapter, $userRepository))->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ChangeExpiredPassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ChangeExpiredPassword
    {
        return $this($serviceLocator, ChangeExpiredPassword::class);
    }
}
