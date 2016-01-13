<?php

/**
 * Transport Manager Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\Tm\TransportManager as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Tm\TransportManager as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;

/**
 * Transport Manager Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TransportManager', TransportManagerRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $bundle = [
            'tmType',
            'tmStatus',
            'homeCd' => [
                'person' => [
                    'title'
                ],
                'address' => [
                    'countryCode'
                ]
            ],
            'workCd' => [
                'address' => [
                    'countryCode'
                ]
            ],
            'users',
            'mergeToTransportManager' => [
                'homeCd' => ['person']
            ]
        ];
        $query = Query::create(['id' => 1]);

        $mock = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('getUsers')
            ->andReturn([1,2,3])
            ->shouldReceive('getMergeToTransportManager')
            ->andReturn(null)
            ->shouldReceive('serialize')->with($bundle)
            ->once()
            ->andReturn(['foo'])
            ->getMock();

        $this->repoMap['TransportManager']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mock);

        $this->assertSame(
            ['foo', 'hasUsers' => [1,2,3], 'hasBeenMerged' => false],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
