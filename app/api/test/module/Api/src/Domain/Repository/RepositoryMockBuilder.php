<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Mockery as m;
use Mockery\MockInterface;

class RepositoryMockBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    protected $repositoryClass;

    /**
     * @var null|callable
     */
    protected $entityBuilder;

    /**
     * @var string|null
     */
    protected $entityClass = null;

    /**
     * @param string $repositoryClass
     * @param string $entityClass
     */
    public function __construct(string $repositoryClass, string $entityClass = null)
    {
        $this->repositoryClass = $repositoryClass;
        $this->entityClass = $entityClass;
    }

    /**
     * @param callable|null $entityBuilder
     * @return self
     */
    public function setEntityBuilder(?callable $entityBuilder): self
    {
        $this->entityBuilder = $entityBuilder;
        return $this;
    }

    /**
     * Builds an entity to be returned by a repository.
     *
     * @param mixed $id
     * @return mixed
     */
    protected function buildEntity($id)
    {
        if ($this->entityBuilder) {
            return call_user_func($this->entityBuilder, $id);
        }
        return $this->buildDefaultEntity($id);
    }

    /**
     * Builds an entity if no entity builder has been provided.
     *
     * @param mixed $id
     * @return mixed
     */
    protected function buildDefaultEntity($id)
    {
        $args = [];
        if (null !== $this->entityClass) {
            $args[] = $this->entityClass;
        }
        $entity = m::mock(...$args);
        $entity->shouldIgnoreMissing();
        $entity->shouldReceive('getId')->andReturn($id)->byDefault();
        return $entity;
    }

    /**
     * Gets a mock from the current configuration of a builder.
     *
     * @return MockInterface
     */
    public function build()
    {
        $mock = m::mock($this->repositoryClass);
        $mock->shouldIgnoreMissing();
        $mock->shouldReceive('fetchById')->andReturnUsing(function ($entityId) {
            return $this->buildEntity($entityId);
        })->byDefault();
        $mock->shouldReceive('getReference')->andReturnUsing(function ($entityClass, $entityId) {
            $entity = new $entityClass();
            $entity->setId($entityId);
            return $entity;
        })->byDefault();
        $mock->shouldReceive('getRefdataReference')->andReturnUsing(function ($key) {
            return new RefData($key);
        })->byDefault();
        $mock->shouldReceive('getCategoryReference')->andReturnUsing(function ($key) {
            return (new Category())->setId($key);
        })->byDefault();
        $mock->shouldReceive('getSubCategoryReference')->andReturnUsing(function ($key) {
            return (new SubCategory())->setId($key);
        })->byDefault();
        return $mock;
    }
}
