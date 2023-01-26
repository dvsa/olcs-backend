<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PermitUsageUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitUsageUpdater
    {
        return $this->__invoke($serviceLocator, PermitUsageUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PermitUsageUpdater
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageUpdater
    {
        return new PermitUsageUpdater(
            $container->get('PermitsBilateralCommonModifiedAnswerUpdater')
        );
    }
}
