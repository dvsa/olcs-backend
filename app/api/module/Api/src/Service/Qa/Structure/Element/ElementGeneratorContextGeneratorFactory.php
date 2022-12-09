<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ElementGeneratorContextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ElementGeneratorContextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ElementGeneratorContextGenerator
    {
        return $this->__invoke($serviceLocator, ElementGeneratorContextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ElementGeneratorContextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ElementGeneratorContextGenerator
    {
        return new ElementGeneratorContextGenerator(
            $container->get('QaValidatorListGenerator'),
            $container->get('QaElementGeneratorContextFactory')
        );
    }
}
