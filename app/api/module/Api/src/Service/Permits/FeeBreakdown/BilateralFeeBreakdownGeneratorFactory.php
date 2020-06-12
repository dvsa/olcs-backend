<?php

namespace Dvsa\Olcs\Api\Service\Permits\FeeBreakdown;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BilateralFeeBreakdownGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralFeeBreakdownGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new BilateralFeeBreakdownGenerator(
            $serviceLocator->get('RepositoryServiceManager')->get('FeeType')
        );
    }
}
