<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsAnswerFetcherFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsAnswerFetcher
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsAnswerFetcher
    {
        return $this->__invoke($serviceLocator, NoOfPermitsAnswerFetcher::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsAnswerFetcher
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsAnswerFetcher
    {
        return new NoOfPermitsAnswerFetcher(
            $container->get('QaNamedAnswerFetcher')
        );
    }
}
