<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PeriodGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PeriodGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PeriodGenerator
    {
        return $this->__invoke($serviceLocator, PeriodGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PeriodGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeriodGenerator
    {
        $periodGenerator = new PeriodGenerator(
            $container->get('RepositoryServiceManager')->get('IrhpPermitStock')
        );
        $periodGenerator->registerFieldsGenerator(
            Behaviour::STANDARD,
            $container->get('PermitsBilateralMetadataStandardFieldsGenerator')
        );
        $periodGenerator->registerFieldsGenerator(
            Behaviour::MOROCCO,
            $container->get('PermitsBilateralMetadataMoroccoFieldsGenerator')
        );
        return $periodGenerator;
    }
}
