<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\OlcsTest\Api\Domain\Repository\m;
use Dvsa\OlcsTest\Builder\BuilderInterface;
use Mockery;
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
     * @param string $repositoryClass
     */
    public function __construct(string $repositoryClass)
    {
        $this->repositoryClass = $repositoryClass;
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
        $entity = m::mock();
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
        $mock = Mockery::mock($this->repositoryClass);
        $mock->shouldReceive('fetchById')->andReturnUsing(function ($entityId) {
            return $this->buildEntity($entityId);
        })->byDefault();
        $mock->shouldReceive('getReference')->andReturnUsing(function ($key) {
            return new RefData($key);
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
