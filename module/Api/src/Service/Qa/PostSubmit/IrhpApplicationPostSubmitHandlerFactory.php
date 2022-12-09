<?php

namespace Dvsa\Olcs\Api\Service\Qa\PostSubmit;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IrhpApplicationPostSubmitHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpApplicationPostSubmitHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpApplicationPostSubmitHandler
    {
        return $this->__invoke($serviceLocator, IrhpApplicationPostSubmitHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpApplicationPostSubmitHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IrhpApplicationPostSubmitHandler
    {
        return new IrhpApplicationPostSubmitHandler(
            $container->get('RepositoryServiceManager')->get('IrhpPermit')
        );
    }
}
