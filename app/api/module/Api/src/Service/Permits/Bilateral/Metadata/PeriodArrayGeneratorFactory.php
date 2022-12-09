<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PeriodArrayGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PeriodArrayGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PeriodArrayGenerator
    {
        return $this->__invoke($serviceLocator, PeriodArrayGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PeriodArrayGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeriodArrayGenerator
    {
        return new PeriodArrayGenerator(
            $container->get('RepositoryServiceManager')->get('IrhpPermitStock'),
            $container->get('PermitsBilateralMetadataPeriodGenerator'),
            $container->get('CommonCurrentDateTimeFactory')
        );
    }
}
