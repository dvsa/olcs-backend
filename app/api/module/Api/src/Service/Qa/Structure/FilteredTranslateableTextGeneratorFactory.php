<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class FilteredTranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FilteredTranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FilteredTranslateableTextGenerator
    {
        return $this->__invoke($serviceLocator, FilteredTranslateableTextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FilteredTranslateableTextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FilteredTranslateableTextGenerator
    {
        return new FilteredTranslateableTextGenerator(
            $container->get('QaFilteredTranslateableTextFactory'),
            $container->get('QaTranslateableTextGenerator')
        );
    }
}
