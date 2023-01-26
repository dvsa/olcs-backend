<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class HeaderAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return HeaderAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator): HeaderAnswersSummaryRowsAdder
    {
        return $this->__invoke($serviceLocator, HeaderAnswersSummaryRowsAdder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return HeaderAnswersSummaryRowsAdder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): HeaderAnswersSummaryRowsAdder
    {
        return new HeaderAnswersSummaryRowsAdder(
            $container->get('PermitsAnswersSummaryRowFactory'),
            $container->get('ViewRenderer')
        );
    }
}
