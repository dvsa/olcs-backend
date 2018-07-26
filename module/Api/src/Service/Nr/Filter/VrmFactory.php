<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Filter\Vrm as TransferVrmFilter;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new Vrm();
        $service->setVrmFilter($container->get(TransferVrmFilter::class));

        return $service;
    }
}
