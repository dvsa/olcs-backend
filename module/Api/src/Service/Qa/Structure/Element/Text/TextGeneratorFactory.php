<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TextGenerator
    {
        return $this->__invoke($serviceLocator, TextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TextGenerator
    {
        return new TextGenerator(
            $container->get('QaTextElementFactory'),
            $container->get('QaTranslateableTextGenerator')
        );
    }
}
