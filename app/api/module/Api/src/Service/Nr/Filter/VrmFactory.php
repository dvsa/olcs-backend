<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter;

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
        $service = new Vrm();
        $service->setVrmFilter($serviceLocator->get(TransferVrmFilter::class));

        return $service;
    }
}
