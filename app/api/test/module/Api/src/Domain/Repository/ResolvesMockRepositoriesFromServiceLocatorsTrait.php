<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery\MockInterface;

trait ResolvesMockRepositoriesFromServiceLocatorsTrait
{
    /**
     * Resolves a repository using a service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $repositoryName
     * @return MockInterface
     */
    protected function resolveMockRepository(ServiceLocatorInterface $serviceLocator, string $repositoryName): MockInterface
    {
        $repositoryManager = $serviceLocator->get(RepositoryServiceManagerBuilder::ALIAS);
        assert($repositoryManager instanceof RepositoryServiceManager, 'Expected instance of RepositoryServiceManager');
        $repositoryName = $repositoryManager->get($repositoryName);
        return $repositoryName;
    }
}
