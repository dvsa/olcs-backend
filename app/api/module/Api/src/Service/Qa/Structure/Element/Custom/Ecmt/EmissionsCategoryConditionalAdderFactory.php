<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EmissionsCategoryConditionalAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EmissionsCategoryConditionalAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EmissionsCategoryConditionalAdder
    {
        return $this->__invoke($serviceLocator, EmissionsCategoryConditionalAdder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EmissionsCategoryConditionalAdder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EmissionsCategoryConditionalAdder
    {
        return new EmissionsCategoryConditionalAdder(
            $container->get('QaEcmtEmissionsCategoryFactory'),
            $container->get('PermitsAvailabilityEmissionsCategoryAvailabilityCounter')
        );
    }
}
