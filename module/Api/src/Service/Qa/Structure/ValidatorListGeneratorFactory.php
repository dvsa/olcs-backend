<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ValidatorListGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ValidatorListGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ValidatorListGenerator
    {
        return $this->__invoke($serviceLocator, ValidatorListGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ValidatorListGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidatorListGenerator
    {
        return new ValidatorListGenerator(
            $container->get('QaValidatorListFactory'),
            $container->get('QaValidatorGenerator')
        );
    }
}
