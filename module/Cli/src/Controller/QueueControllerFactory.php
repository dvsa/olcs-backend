<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Queue Controller Factory
 */
class QueueControllerFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpCandidatePermitsCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueueController
    {
        $config = $container->get('config');
        $queueService = $container->get('Queue');
        $queryHandlerManager = $container->get('QueryHandlerManager');
        $commandHandlerManager = $container->get('CommandHandlerManager');

        return new QueueController(
            $config,
            $queueService,
            $queryHandlerManager,
            $commandHandlerManager
        );
    }
}
