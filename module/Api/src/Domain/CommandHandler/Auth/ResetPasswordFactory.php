<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see ResetPasswordFactory
 */
class ResetPasswordFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ResetPassword
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ResetPassword
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $eventHistoryCreator = $sl->get('EventHistoryCreator');
        $instance = new ResetPassword($adapter, $eventHistoryCreator);
        return $instance->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ResetPassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): ResetPassword
    {
        return $this($serviceLocator, ResetPassword::class);
    }
}
