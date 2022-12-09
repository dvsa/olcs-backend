<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\Olcs\Api\Domain\Query\FeatureToggle\FetchList;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Qandidate\Toggle\Serializer\InMemoryCollectionSerializer;
use Qandidate\Toggle\ToggleManager;
use Interop\Container\ContainerInterface;

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
        return $this->__invoke($serviceLocator, ToggleService::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ToggleService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ToggleService
    {
        /**
         * @var FeatureToggleRepo $repo
         */
        $repo = $container->get('RepositoryServiceManager')->get('FeatureToggle');
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
