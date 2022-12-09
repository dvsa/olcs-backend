<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ApplicationCountryRemoverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationCountryRemover
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationCountryRemover
    {
        return $this->__invoke($serviceLocator, ApplicationCountryRemover::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationCountryRemover
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationCountryRemover
    {
        return new ApplicationCountryRemover(
            $container->get('CqrsCommandCreator'),
            $container->get('CommandHandlerManager')
        );
    }
}
