<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

/**
 * SeriousInfringementTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SeriousInfringementTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement::class, true);
    }

    public function testApplyListFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\Cases\Si\GetList::create(['case' => 812]);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.case = [[812]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
