<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IrhpPermitApplicationCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitApplicationCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpPermitApplicationCreator
    {
        return $this->__invoke($serviceLocator, IrhpPermitApplicationCreator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpPermitApplicationCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IrhpPermitApplicationCreator
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new IrhpPermitApplicationCreator(
            $repoServiceManager->get('IrhpPermitStock'),
            $repoServiceManager->get('IrhpPermitApplication'),
            $container->get('PermitsBilateralInternalIrhpPermitApplicationFactory')
        );
    }
}
