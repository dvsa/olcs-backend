<?php

namespace Dvsa\Olcs\Api\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Generic Controller Factory
 */
class GenericControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GenericController
    {
        return $this->__invoke($serviceLocator, GenericController::class);
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GenericController
    {
        $sm = $container->getServiceLocator();
        $queryHandlerManager = $sm->get('QueryHandlerManager');
        $commandHandlerManager = $sm->get('CommandHandlerManager');

        return new GenericController(
            $queryHandlerManager,
            $commandHandlerManager
        );
    }
}
