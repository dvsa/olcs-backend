<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SelfservePageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SelfservePageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SelfservePageGenerator
    {
        return $this->__invoke($serviceLocator, SelfservePageGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SelfservePageGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SelfservePageGenerator
    {
        return new SelfservePageGenerator(
            $container->get('QaSelfservePageFactory'),
            $container->get('QaApplicationStepGenerator'),
            $container->get('FormControlServiceManager'),
            $container->get('QaContextFactory')
        );
    }
}
