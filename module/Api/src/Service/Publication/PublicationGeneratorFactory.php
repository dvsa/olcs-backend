<?php

namespace Dvsa\Olcs\Api\Service\Publication;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager as ContextPluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager as ProcessPluginManager;

/**
 * Class PublicationGeneratorFactory
 * @package Dvsa\Olcs\Api\Service\Publication
 */
class PublicationGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PublicationGenerator(
            $container->get('Config')['publications'],
            $container->get(ContextPluginManager::class),
            $container->get(ProcessPluginManager::class)
        );
    }
}
