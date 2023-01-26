<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\Logger;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Psr\Container\ContainerInterface;

/**
 * @see EntityAccessLogger
 * @see \Dvsa\OlcsTest\Api\Logger\EntityAccessLoggerFactoryTest
 */
class EntityAccessLoggerFactory implements FactoryInterface
{

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EntityAccessLogger
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EntityAccessLogger
    {
        return new EntityAccessLogger(
            $container->get(AuthorizationService::class),
            $container->get('CommandHandlerManager')
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return EntityAccessLogger
     * @deprecated Use __invoke instead
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EntityAccessLogger
    {
        return $this($serviceLocator, EntityAccessLogger::class);
    }
}
