<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class JsonDecodingFilteredTranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return JsonDecodingFilteredTranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): JsonDecodingFilteredTranslateableTextGenerator
    {
        return $this->__invoke($serviceLocator, JsonDecodingFilteredTranslateableTextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return JsonDecodingFilteredTranslateableTextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): JsonDecodingFilteredTranslateableTextGenerator
    {
        return new JsonDecodingFilteredTranslateableTextGenerator(
            $container->get('QaFilteredTranslateableTextGenerator')
        );
    }
}
