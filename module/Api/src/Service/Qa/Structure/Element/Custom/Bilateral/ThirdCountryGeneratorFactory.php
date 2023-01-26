<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ThirdCountryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ThirdCountryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ThirdCountryGenerator
    {
        return $this->__invoke($serviceLocator, ThirdCountryGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ThirdCountryGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThirdCountryGenerator
    {
        return new ThirdCountryGenerator(
            $container->get('QaBilateralThirdCountryElementFactory')
        );
    }
}
