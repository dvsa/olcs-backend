<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ResetPasswordOpenAmFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ResetPasswordOpenAm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ResetPasswordOpenAm
    {
        $adapter = $container->get(ValidatableAdapterInterface::class);
        $eventHistoryCreator = $container->get('EventHistoryCreator');
        $instance = new ResetPasswordOpenAm($adapter, $eventHistoryCreator);
        return $instance->__invoke($container, $requestedName, $options);
    }
}
