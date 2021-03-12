<?php

namespace Dvsa\OlcsTest;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\OlcsTest\Api\Domain\Repository\RepositoryMockBuilder;
use Dvsa\OlcsTest\Api\Domain\Repository\RepositoryServiceManagerBuilder;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery\MockInterface;

trait MocksRepositoriesTrait
{
    /**
     * @return array
     */
    abstract protected function setUpDefaultRepositories(): array;

    /**
     * @return RepositoryServiceManager
     */
    protected function setUpRepositoryServiceManager(): RepositoryServiceManager
    {
        return (new RepositoryServiceManagerBuilder($this->setUpDefaultRepositories()))->build();
    }

    /**
     * @param string $repositoryClass
     * @param string $entityClass
     * @return MockInterface
     */
    protected function setUpMockRepository(string $repositoryClass, string $entityClass): MockInterface
    {
        return (new RepositoryMockBuilder($repositoryClass, $entityClass))->build();
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $repositoryName
     * @return MockInterface
     */
    protected function resolveMockRepository(ServiceLocatorInterface $serviceLocator, string $repositoryName): MockInterface
    {
        $manager = $serviceLocator->get('RepositoryServiceManager');
        assert($manager instanceof RepositoryServiceManager, 'Expected instance of RepositoryServiceManager');
        return $manager->get($repositoryName);
    }
}
