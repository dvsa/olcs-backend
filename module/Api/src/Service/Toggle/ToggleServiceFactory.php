<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

<<<<<<< HEAD
=======
use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\Olcs\Api\Domain\Query\FeatureToggle\FetchList;
>>>>>>> 691972783682db5f4d601133be6b3ce964fc2f3b
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Qandidate\Toggle\Serializer\InMemoryCollectionSerializer;
use Qandidate\Toggle\ToggleManager;

/**
 * Class ToggleServiceFactory
 */
class ToggleServiceFactory implements FactoryInterface
{
<<<<<<< HEAD
    const CONFIG_KEY = 'feature_toggle';

=======
>>>>>>> 691972783682db5f4d601133be6b3ce964fc2f3b
    /**
     * Create the toggle service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return ToggleService
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ToggleService
    {
<<<<<<< HEAD
        /** @var array $config */
        $config = $serviceLocator->get('Config');
        $collectionSerializer = new InMemoryCollectionSerializer();
        $collection = $collectionSerializer->deserialize($config[self::CONFIG_KEY]);
=======
        /**
         * @var FeatureToggleRepo $repo
         */
        $repo = $serviceLocator->get('RepositoryServiceManager')->get('FeatureToggle');
        $toggleConfig = $repo->fetchList(FetchList::create([]));

        $configArray = [];

        foreach ($toggleConfig as $toggle) {
            $configArray[$toggle['friendlyName']] = [
                'name' => $toggle['configName'],
                'conditions' => [],
                'status' => $toggle['status']['id']
            ];
        }

        $collectionSerializer = new InMemoryCollectionSerializer();
        $collection = $collectionSerializer->deserialize($configArray);
>>>>>>> 691972783682db5f4d601133be6b3ce964fc2f3b

        return new ToggleService(
            new ToggleManager($collection)
        );
    }
}
