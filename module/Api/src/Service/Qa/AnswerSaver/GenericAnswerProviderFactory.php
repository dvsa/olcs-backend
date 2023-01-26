<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class GenericAnswerProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GenericAnswerProvider
    {
        return $this->__invoke($serviceLocator, GenericAnswerProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GenericAnswerProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GenericAnswerProvider
    {
        return new GenericAnswerProvider(
            $container->get('RepositoryServiceManager')->get('Answer')
        );
    }
}
