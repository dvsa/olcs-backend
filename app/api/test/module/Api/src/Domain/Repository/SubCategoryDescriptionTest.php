<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

/**
 * SubCategoryDescriptionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategoryDescriptionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\SubCategoryDescription::class, true);
    }

    public function testApplyListFiltersNoFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC', [])->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\SubCategoryDescription\GetList::create(
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
            ->shouldReceive('order')->with('id', 'ASC', [])->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\SubCategoryDescription\GetList::create(
            [
                'subCategory' => '212',
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.subCategory = [[212]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
