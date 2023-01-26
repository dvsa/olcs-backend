<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BilateralAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BilateralAnswersSummaryRowsAdder
    {
        return $this->__invoke($serviceLocator, BilateralAnswersSummaryRowsAdder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BilateralAnswersSummaryRowsAdder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BilateralAnswersSummaryRowsAdder
    {
        return new BilateralAnswersSummaryRowsAdder(
            $container->get('PermitsAnswersSummaryRowFactory'),
            $container->get('ViewRenderer'),
            $container->get('PermitsBilateralIpaAnswersSummaryRowsAdder')
        );
    }
}
