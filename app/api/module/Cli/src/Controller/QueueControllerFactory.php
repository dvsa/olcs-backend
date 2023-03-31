<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Queue Controller Factory
 */
class QueueControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QueueController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QueueController
    {
        return $this->__invoke($serviceLocator, QueueController::class);
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueueController
    {
        $sm = $container->getServiceLocator();

        $config = $sm->get('config');
        $queueService = $sm->get('Queue');

        return new QueueController(
            $config,
            $queueService
        );
    }
}
