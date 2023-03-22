<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * SQS Controller Factory
 */
class SQSControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QueueController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SQSController
    {
        return $this->__invoke($serviceLocator, SQSController::class);
    }

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
        $sm = $container->getServiceLocator();

        $config = $sm->get('config');
        $commandHandlerManager = $sm->get('CommandHandlerManager');

        return new SQSController(
            $config,
            $commandHandlerManager
        );
    }
}
