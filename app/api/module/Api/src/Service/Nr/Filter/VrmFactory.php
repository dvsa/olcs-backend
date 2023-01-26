<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Filter\Vrm as TransferVrmFilter;
use Interop\Container\ContainerInterface;

/**
 * Class VrmFactory
 * @package Dvsa\Olcs\Api\Service\Nr\Filter
 */
class VrmFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Vrm
    {
        return $this->__invoke($serviceLocator, Vrm::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Vrm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Vrm
    {
        $service = new Vrm();
        $service->setVrmFilter($container->get(TransferVrmFilter::class));
        return $service;
    }
}
