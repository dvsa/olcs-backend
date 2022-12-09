<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TranslateableTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslateableTextGenerator
    {
        return $this->__invoke($serviceLocator, TranslateableTextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TranslateableTextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateableTextGenerator
    {
        return new TranslateableTextGenerator(
            $container->get('QaTranslateableTextFactory'),
            $container->get('QaTranslateableTextParameterGenerator')
        );
    }
}
