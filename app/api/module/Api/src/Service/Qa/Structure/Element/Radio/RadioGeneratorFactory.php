<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RadioGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RadioGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RadioGenerator
    {
        return $this->__invoke($serviceLocator, RadioGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RadioGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RadioGenerator
    {
        return new RadioGenerator(
            $container->get('QaRadioElementFactory'),
            $container->get('QaOptionListGenerator'),
            $container->get('QaTranslateableTextGenerator')
        );
    }
}
