<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TranslateableTextParameterGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextParameterGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslateableTextParameterGenerator
    {
        return $this->__invoke($serviceLocator, TranslateableTextParameterGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TranslateableTextParameterGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateableTextParameterGenerator
    {
        return new TranslateableTextParameterGenerator(
            $container->get('QaTranslateableTextParameterFactory')
        );
    }
}
