<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ClientReturnCodeHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ClientReturnCodeHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ClientReturnCodeHandler
    {
        return $this->__invoke($serviceLocator, ClientReturnCodeHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ClientReturnCodeHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ClientReturnCodeHandler
    {
        return new ClientReturnCodeHandler(
            $container->get('PermitsBilateralApplicationCountryRemover')
        );
    }
}
