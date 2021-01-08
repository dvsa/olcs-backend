<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\OlcsTest\Builder\BuilderInterface;

class RepositoryServiceManagerBuilder implements BuilderInterface
{
    const ALIAS = 'RepositoryServiceManager';

    /**
     * @var array
     */
    protected $repositories;

    /**
     * @param array $repositories
     */
    public function __construct(array $repositories = [])
    {
        $this->repositories = $repositories;
    }

    /**
     * @return RepositoryServiceManager
     */
    public function build()
    {
        $registry = new RepositoryServiceManager();
        $registry->setAllowOverride(true);
        foreach ($this->repositories as $repositoryName => $repository) {
            $registry->setService($repositoryName, $repository);
        }
        return $registry;
    }
}
