<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PermitStartDateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitStartDateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitStartDateGenerator
    {
        return $this->__invoke($serviceLocator, PermitStartDateGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PermitStartDateGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitStartDateGenerator
    {
        return new PermitStartDateGenerator(
            $container->get('QaCommonDateWithThresholdElementGenerator')
        );
    }
}
