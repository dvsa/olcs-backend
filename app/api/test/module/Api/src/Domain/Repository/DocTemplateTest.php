<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

/**
 * DocTemplateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DocTemplateTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\DocTemplate::class, true);
    }

    public function testApplyListFiltersNoFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\DocTemplate\GetList::create(
            []
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testApplyListFiltersAllN()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\DocTemplate\GetList::create(
            [
                'category' => 1,
                'subCategory' => 12,
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.category = [[1]] AND '
            . 'm.subCategory = [[12]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
