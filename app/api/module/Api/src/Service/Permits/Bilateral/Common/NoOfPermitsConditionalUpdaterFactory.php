<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsConditionalUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsConditionalUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsConditionalUpdater
    {
        return $this->__invoke($serviceLocator, NoOfPermitsConditionalUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsConditionalUpdater
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsConditionalUpdater
    {
        return new NoOfPermitsConditionalUpdater(
            $container->get('PermitsBilateralCommonNoOfPermitsUpdater')
        );
    }
}
