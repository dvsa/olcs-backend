<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class FeeCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeCreatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeCreator
    {
        return $this->__invoke($serviceLocator, FeeCreator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FeeCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeeCreator
    {
        return new FeeCreator(
            $container->get('RepositoryServiceManager')->get('FeeType'),
            $container->get('CqrsCommandCreator'),
            $container->get('CommandHandlerManager'),
            $container->get('CommonCurrentDateTimeFactory')
        );
    }
}
