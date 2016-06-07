<?php

/**
 * Result Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;

/**
 * Result Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResultTest extends MockeryTestCase
{
    public function testResultWithEntity()
    {
        $expected = ['foo' => 'bar', 'cake' => 'bar'];

        /** @var BundleSerializableInterface $entity */
        $entity = m::mock(BundleSerializableInterface::class);

        $entity->shouldReceive('serialize')->once()->with([])->andReturn(['foo' => 'bar', 'cake' => 'bar']);

        $result = new Result($entity);
        $this->assertEquals($expected, $result->serialize());
    }

    public function testResultWithEntityAndBundle()
    {
        $expected = ['foo' => 'bar', 'cake' => 'bar'];

        /** @var BundleSerializableInterface $entity */
        $entity = m::mock(BundleSerializableInterface::class);

        $entity->shouldReceive('serialize')->once()->with(['blah' => 'blah'])
            ->andReturn(['foo' => 'bar', 'cake' => 'bar']);

        $result = new Result($entity, ['blah' => 'blah']);
        $this->assertEquals($expected, $result->serialize());
    }

    public function testResultWithEntityAndBundleAndData()
    {
        $data = ['cake' => 'mix', 'stuff' => 'foo'];
        $expected = ['foo' => 'bar', 'cake' => 'mix', 'stuff' => 'foo'];

        /** @var BundleSerializableInterface $entity */
        $entity = m::mock(BundleSerializableInterface::class);

        $entity->shouldReceive('serialize')->once()->with(['blah' => 'blah'])
            ->andReturn(['foo' => 'bar', 'cake' => 'bar']);

        $result = new Result($entity, ['blah' => 'blah'], $data);
        $this->assertEquals($expected, $result->serialize());
    }

    public function testResultWithEntityAndBundleAndDataSetValue()
    {
        $data = ['cake' => 'mix', 'stuff' => 'foo'];
        $expected = ['foo' => 'bar', 'cake' => 'choc', 'stuff' => 'foo'];

        /** @var BundleSerializableInterface $entity */
        $entity = m::mock(BundleSerializableInterface::class);

        $entity->shouldReceive('serialize')->once()->with(['blah' => 'blah'])
            ->andReturn(['foo' => 'bar', 'cake' => 'bar']);

        $result = new Result($entity, ['blah' => 'blah'], $data);
        $result->setValue('cake', 'choc');
        $this->assertEquals($expected, $result->serialize());
    }

    public function testResult()
    {
        $result = new Result(m::mock(BundleSerializableInterface::class));
        $this->assertFalse($result->isEmpty());
    }
}
