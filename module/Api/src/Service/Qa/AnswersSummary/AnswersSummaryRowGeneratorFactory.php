<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnswersSummaryRowGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryRowGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnswersSummaryRowGenerator
    {
        return $this->__invoke($serviceLocator, AnswersSummaryRowGenerator::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnswersSummaryRowGenerator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnswersSummaryRowGenerator
    {
        return new AnswersSummaryRowGenerator(
            $container->get('PermitsAnswersSummaryRowFactory'),
            $container->get('ViewRenderer'),
            $container->get('QaContextFactory'),
            $container->get('QaElementGeneratorContextGenerator')
        );
    }
}
