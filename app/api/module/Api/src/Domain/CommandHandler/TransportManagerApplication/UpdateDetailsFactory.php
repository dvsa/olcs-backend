<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\AcquiredRights\Service\AcquiredRightsService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UpdateDetailsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return UpdateDetails
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        assert($container instanceof ServiceLocatorAwareInterface);
        $pluginManager = $container;
        $container = $container->getServiceLocator();

        $acquiredServiceService = $container->get(AcquiredRightsService::class);
        $instance = new UpdateDetails($acquiredServiceService);
        return $instance->createService($pluginManager);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UpdateDetails
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, UpdateDetails::class);
    }
}
