<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class FeeUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeUpdater
    {
        return $this->__invoke($serviceLocator, FeeUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FeeUpdater
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeeUpdater
    {
        return new FeeUpdater(
            $container->get('CqrsCommandCreator'),
            $container->get('CommandHandlerManager'),
            $container->get('PermitsFeesEcmtApplicationFeeCommandCreator')
        );
    }
}
