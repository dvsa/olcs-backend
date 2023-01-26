<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TotAuthVehiclesGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TotAuthVehiclesGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TotAuthVehiclesGenerator
    {
        return $this->__invoke($serviceLocator, TotAuthVehiclesGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TotAuthVehiclesGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TotAuthVehiclesGenerator
    {
        return new TotAuthVehiclesGenerator(
            $container->get('QaTextElementGenerator')
        );
    }
}
