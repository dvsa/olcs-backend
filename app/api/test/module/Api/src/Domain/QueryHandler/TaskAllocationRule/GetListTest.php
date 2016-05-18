<?php
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule\GetList as QueryHandler;
use Dvsa\Olcs\Transfer\Query\TaskAllocationRule\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Mockery as m;

/**
 * TaskAllocationRule GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TaskAllocationRule', \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create([]);

        $mockTas = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->with(
                [
                    'category',
                    'team',
                    'user' => ['contactDetails' => ['person']],
                    'trafficArea',
                    'taskAlphaSplits' => ['user' => ['contactDetails' => ['person']]],
                ]
            )
            ->once()
            ->andReturn(['foo' => 'bar'])
            ->getMock();

        $this->repoMap['TaskAllocationRule']->shouldReceive('fetchList')
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)->once()->andReturn([$mockTas]);
        $this->repoMap['TaskAllocationRule']->shouldReceive('fetchCount')->with($query)->once()->andReturn(1);
        $this->repoMap['TaskAllocationRule']->shouldReceive('hasRows')->with($query)->once()->andReturn(true);

        $this->assertSame(
            [
                'result' => [
                    ['foo' => 'bar']
                ],
                'count' => 1,
                'count-unfiltered' => true,
            ],
            $this->sut->handleQuery($query)
        );
    }
}
