<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\Olcs\Api\Domain\Query\FeatureToggle\FetchList;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Qandidate\Toggle\Serializer\InMemoryCollectionSerializer;
use Qandidate\Toggle\ToggleManager;

/**
 * Class ToggleServiceFactory
 */
class ToggleServiceFactory implements FactoryInterface
{
    /**
     * Create the toggle service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return ToggleService
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ToggleService
    {
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

        return new ToggleService(
            new ToggleManager($collection)
        );
    }
}
