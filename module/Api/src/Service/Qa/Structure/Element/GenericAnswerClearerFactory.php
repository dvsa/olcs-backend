<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class GenericAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GenericAnswerClearer
    {
        return $this->__invoke($serviceLocator, GenericAnswerClearer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GenericAnswerClearer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GenericAnswerClearer
    {
        return new GenericAnswerClearer(
            $container->get('QaGenericAnswerProvider'),
            $container->get('RepositoryServiceManager')->get('Answer')
        );
    }
}
