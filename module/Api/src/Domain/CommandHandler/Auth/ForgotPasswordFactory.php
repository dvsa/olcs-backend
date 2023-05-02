<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Auth\Service\PasswordService;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ForgotPassword
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ForgotPassword
    {
        $pluginManager = $container;
        $container = $container->getServiceLocator();

        $authAdapter = $container->get(ValidatableAdapterInterface::class);
        $passwordService = $container->get(PasswordService::class);

        $instance = new ForgotPassword($authAdapter, $passwordService);
        return $instance->createService($pluginManager);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): ForgotPassword
    {
        return $this($serviceLocator, ForgotPassword::class);
    }
}
