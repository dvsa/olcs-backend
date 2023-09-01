<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * SQS Controller Factory
 */
class SQSControllerFactory implements FactoryInterface
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SQSController
    {
        $config = $container->get('config');
        $commandHandlerManager = $container->get('CommandHandlerManager');

        return new SQSController(
            $config,
            $commandHandlerManager
        );
    }
}
