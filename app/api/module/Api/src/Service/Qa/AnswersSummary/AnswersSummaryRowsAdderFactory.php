<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnswersSummaryRowsAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryRowsAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnswersSummaryRowsAdder
    {
        return $this->__invoke($serviceLocator, AnswersSummaryRowsAdder::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnswersSummaryRowsAdder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnswersSummaryRowsAdder
    {
        return new AnswersSummaryRowsAdder(
            $container->get('QaSupplementedApplicationStepsProvider'),
            $container->get('QaAnswersSummaryRowGenerator')
        );
    }
}
