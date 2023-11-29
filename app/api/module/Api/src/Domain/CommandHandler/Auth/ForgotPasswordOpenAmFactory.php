<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ForgotPasswordOpenAmFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ForgotPasswordOpenAm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ForgotPasswordOpenAm
    {
        $adapter = $container->get(ValidatableAdapterInterface::class);
        $translator = $container->get('translator');
        $instance = new ForgotPasswordOpenAm($adapter, $translator);
        return $instance->__invoke($container, $requestedName, $options);
    }
}
