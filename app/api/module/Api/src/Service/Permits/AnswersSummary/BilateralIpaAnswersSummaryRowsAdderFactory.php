<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BilateralIpaAnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BilateralIpaAnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BilateralIpaAnswersSummaryRowsAdder
    {
        return $this->__invoke($serviceLocator, BilateralIpaAnswersSummaryRowsAdder::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BilateralIpaAnswersSummaryRowsAdder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BilateralIpaAnswersSummaryRowsAdder
    {
        return new BilateralIpaAnswersSummaryRowsAdder(
            $container->get('PermitsAnswersSummaryRowFactory'),
            $container->get('ViewRenderer'),
            $container->get('QaAnswersSummaryRowsAdder'),
            $container->get('RepositoryServiceManager')->get('IrhpPermitStock')
        );
    }
}
