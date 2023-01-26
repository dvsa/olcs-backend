<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsMoroccoGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsMoroccoGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsMoroccoGenerator
    {
        return $this->__invoke($serviceLocator, NoOfPermitsMoroccoGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsMoroccoGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsMoroccoGenerator
    {
        return new NoOfPermitsMoroccoGenerator(
            $container->get('QaBilateralNoOfPermitsMoroccoElementFactory')
        );
    }
}
