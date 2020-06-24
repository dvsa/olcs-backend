<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule\GetList as QueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\TaskAllocationRule\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * TaskAllocationRule GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();

        $this->mockRepo('TaskAllocationRule', \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create([]);

        $mockUser = m::mock(Entity\User\User::class)
            ->shouldReceive('getDeletedDate')->twice()->andReturn(new \DateTime())
            ->getMock();

        $mockRule = m::mock(Entity\Task\TaskAllocationRule::class)
            ->shouldReceive('getUser')->once()->andReturn($mockUser)
            ->shouldReceive('setUser')->with(null)->once()
            //
            ->shouldReceive('getTaskAlphaSplits')
            ->once()
            ->andReturn(
                [
                    m::mock(Entity\Task\TaskAlphaSplit::class)
                        ->shouldReceive('getUser')->once()->andReturn($mockUser)
                        ->shouldReceive('setUser')->with(null)->once()->andReturn($mockUser)
                        ->getMock()
                ]
            )
            //
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

        $this->repoMap['TaskAllocationRule']
            ->shouldReceive('fetchList')
            ->once()
            ->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$mockRule])
            //
            ->shouldReceive('fetchCount')->with($query)->once()->andReturn(1)
            ->shouldReceive('hasRows')->with($query)->once()->andReturn(true)
            ->shouldReceive('disableSoftDeleteable')->once();

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
