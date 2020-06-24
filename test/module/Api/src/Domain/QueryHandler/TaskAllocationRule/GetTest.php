<?php
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule\Get as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TaskAllocationRule\Get as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Mockery as m;

/**
 * TaskAllocationRule GetTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TaskAllocationRule', \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 1304]);

        $mockTas = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->with(
                [
                    'category',
                    'team',
                    'user' => ['contactDetails' => ['person']],
                    'trafficArea',
                    'taskAlphaSplits',
                ]
            )
            ->once()
            ->andReturn(['foo' => 'bar'])
            ->getMock();

        $this->repoMap['TaskAllocationRule']->shouldReceive('fetchUsingId')->with($query)->once()->andReturn($mockTas);

        $this->assertSame(
            ['foo' => 'bar'],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
