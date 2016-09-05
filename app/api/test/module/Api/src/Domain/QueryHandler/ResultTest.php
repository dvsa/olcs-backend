<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Domain\QueryHandler\Result
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
        $data = [
            'cake' => [
                'subcakeA' => 'expect',
            ],
            'stuff' => 'foo',
        ];
        $expected = [
            'foo' => 'bar',
            'cake' => [
                'subcakeA' => 'expect',
                'subcakeB' => 'shouldExists',
            ],
            'stuff' => 'foo',
        ];

        /** @var BundleSerializableInterface $entity */
        $entity = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->once()
            ->with(['blah' => 'blah'])
            ->andReturn(
                [
                    'foo' => 'bar',
                    'cake' => [
                        'subcakeA' => 'replaceMe',
                        'subcakeB' => 'shouldExists',
                    ],
                ]
            )
            ->getMock();

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
