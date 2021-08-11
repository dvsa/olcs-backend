<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Mockery\MockInterface;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

trait MocksLicenceRepositoryTrait
{
    /**
     * @var array
     */
    protected $licenceRepositoryStorage = [];

    /**
     * @return RepositoryServiceManager
     */
    abstract protected function repositoryServiceManager(): RepositoryServiceManager;

    /**
     * @param string $class
     * @return MockInterface
     */
    abstract protected function setUpMockService(string $class): MockInterface;

    /**
     * @return MockInterface|LicenceRepository
     */
    protected function licenceRepository(): MockInterface
    {
        $repositoryServiceManager = $this->repositoryServiceManager();
        if (! $repositoryServiceManager->has('Licence')) {
            $instance = $this->setUpMockService(LicenceRepository::class);
            $instance->allows('injectEntity')->andReturnUsing(function (Licence $entity) {
                /* @see \Dvsa\OlcsTest\MocksRepositoriesTrait::injectEntities() */
                $this->licenceRepositoryStorage['id'][$entity->getId()] = $entity;
            });
            $instance->allows('fetchUsingId')->andReturnUsing(function ($command) {
                return $this->licenceRepositoryStorage['id'][$command->getId()] ?? null;
            })->byDefault();
            $instance->allows('fetchById')->andReturnUsing(function (int $id) {
                return $this->licenceRepositoryStorage['id'][$id] ?? null;
            })->byDefault();
            $repositoryServiceManager->setService('Licence', $instance);
            $repositoryServiceManager->setService('RepositoryFor__' . Licence::class, $instance);
        }
        return $repositoryServiceManager->get('Licence');
    }
}
