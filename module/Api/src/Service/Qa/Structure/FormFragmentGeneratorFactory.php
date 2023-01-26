<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class FormFragmentGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormFragmentGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormFragmentGenerator
    {
        return $this->__invoke($serviceLocator, FormFragmentGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FormFragmentGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormFragmentGenerator
    {
        return new FormFragmentGenerator(
            $container->get('QaFormFragmentFactory'),
            $container->get('QaApplicationStepGenerator'),
            $container->get('QaContextFactory')
        );
    }
}
