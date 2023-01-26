<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class GenericAnswerFetcherFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerFetcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GenericAnswerFetcher
    {
        return $this->__invoke($serviceLocator, GenericAnswerFetcher::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GenericAnswerFetcher
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GenericAnswerFetcher
    {
        return new GenericAnswerFetcher(
            $container->get('QaNamedAnswerFetcher')
        );
    }
}
