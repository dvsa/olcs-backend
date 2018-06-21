<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\Olcs\Transfer\Query\FeatureToggle\GetList;
use Olcs\Logging\Log\Logger;
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
        /**
         * @todo - botch job need to improve going forward
         * @var FeatureToggleRepo $repo
         */
        $repo = $serviceLocator->get('RepositoryServiceManager')->get('FeatureToggle');
        $toggleConfig = $repo->fetchList(GetList::create(['page' => 1, 'limit' => 100]));

        $configArray = [];

        foreach ($toggleConfig as $toggle) {
            $configArray[$toggle['friendlyName']] = [
                'name' => $toggle['configName'],
                'conditions' => [],
                'status' => $toggle['status']['id']
            ];
        }

        Logger::debug('toggle config', $configArray);

        $collectionSerializer = new InMemoryCollectionSerializer();
        $collection = $collectionSerializer->deserialize($configArray);

        return new ToggleService(
            new ToggleManager($collection)
        );
    }
}
