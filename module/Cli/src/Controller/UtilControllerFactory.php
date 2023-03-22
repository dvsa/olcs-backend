<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * SQS Controller Factory
 */
class UtilControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UtilController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): UtilController
    {
        return $this->__invoke($serviceLocator, UtilController::class);
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UtilController
    {
        $sm = $container->getServiceLocator();

        $commandHandlerManager = $sm->get('QueryHandlerManager');

        return new UtilController(
            $commandHandlerManager
        );
    }
}
