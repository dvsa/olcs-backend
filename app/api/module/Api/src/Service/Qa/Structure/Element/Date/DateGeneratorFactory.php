<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class DateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DateGenerator
    {
        return $this->__invoke($serviceLocator, DateGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DateGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DateGenerator
    {
        return new DateGenerator(
            $container->get('QaDateElementFactory')
        );
    }
}
