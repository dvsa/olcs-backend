<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsUpdater
    {
        return $this->__invoke($serviceLocator, NoOfPermitsUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsUpdater
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsUpdater
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new NoOfPermitsUpdater(
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('FeeType'),
            $container->get('CqrsCommandCreator'),
            $container->get('CommandHandlerManager'),
            $container->get('PermitsBilateralApplicationFeesClearer'),
            $container->get('CommonCurrentDateTimeFactory')
        );
    }
}
