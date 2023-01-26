<?php

namespace Dvsa\Olcs\Api\Service\Permits\FeeBreakdown;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BilateralFeeBreakdownGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralFeeBreakdownGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BilateralFeeBreakdownGenerator
    {
        return $this->__invoke($serviceLocator, BilateralFeeBreakdownGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BilateralFeeBreakdownGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BilateralFeeBreakdownGenerator
    {
        return new BilateralFeeBreakdownGenerator(
            $container->get('RepositoryServiceManager')->get('FeeType')
        );
    }
}
