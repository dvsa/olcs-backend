<?php

namespace Dvsa\Olcs\Api\Domain\Util;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class SlaCalculator
 * @package Dvsa\Olcs\Api\Domain\Util
 */
final class SlaCalculatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SlaCalculator
    {
        return $this->__invoke($serviceLocator, SlaCalculator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SlaCalculator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SlaCalculator
    {
        return new SlaCalculator($container->get(TimeProcessorBuilderInterface::class));
    }
}
