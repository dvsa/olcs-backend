<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Qandidate\Toggle\Serializer\InMemoryCollectionSerializer;
use Qandidate\Toggle\ToggleManager;

/**
 * Class ToggleServiceFactory
 */
class ToggleServiceFactory implements FactoryInterface
{
    const CONFIG_KEY = 'feature_toggle';

    /**
     * Create the toggle service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return ToggleService
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ToggleService
    {
        /** @var array $config */
        $config = $serviceLocator->get('Config');
        $collectionSerializer = new InMemoryCollectionSerializer();
        $collection = $collectionSerializer->deserialize($config[self::CONFIG_KEY]);

        return new ToggleService(
            new ToggleManager($collection)
        );
    }
}
