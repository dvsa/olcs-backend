<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StandardFieldsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardFieldsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardFieldsGenerator
    {
        return $this->__invoke($serviceLocator, StandardFieldsGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StandardFieldsGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardFieldsGenerator
    {
        return new StandardFieldsGenerator(
            $container->get('PermitsBilateralMetadataCurrentFieldValuesGenerator')
        );
    }
}
