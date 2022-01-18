<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordOpenAmFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ForgotPasswordOpenAm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ForgotPasswordOpenAm
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $translator = $sl->get('translator');
        $instance = new ForgotPasswordOpenAm($adapter, $translator);
        return $instance->createService($container);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPasswordOpenAm
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ForgotPasswordOpenAm
    {
        return $this->__invoke($serviceLocator, ForgotPasswordOpenAm::class);
    }
}
