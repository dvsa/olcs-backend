<?php

namespace Dvsa\Olcs\Api\Service\Publication;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
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
        return new PublicationGenerator(
            $serviceLocator->get('Config')['publications'],
            $serviceLocator->get(ContextPluginManager::class),
            $serviceLocator->get(ProcessPluginManager::class)
        );
    }
}
