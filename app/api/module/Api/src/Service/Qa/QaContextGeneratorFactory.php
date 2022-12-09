<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class QaContextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QaContextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QaContextGenerator
    {
        return $this->__invoke($serviceLocator, QaContextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return QaContextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QaContextGenerator
    {
        return new QaContextGenerator(
            $container->get('RepositoryServiceManager')->get('ApplicationStep'),
            $container->get('QaEntityProvider'),
            $container->get('QaContextFactory')
        );
    }
}
