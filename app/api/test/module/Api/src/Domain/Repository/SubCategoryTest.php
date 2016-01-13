<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

/**
 * SubCategoryTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubCategoryTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\SubCategory::class, true);
    }

    public function testApplyListFiltersNoFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\SubCategory\GetList::create(
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
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\SubCategory\GetList::create(
            [
                'isTaskCategory' => 'N',
                'isDocCategory' => 'N',
                'isScanCategory' => 'N',
                'category' => '3011',
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.isTask = [[false]] AND '
            . 'm.isDoc = [[false]] AND '
            . 'm.isScan = [[false]] AND '
            . 'm.category = [[3011]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testApplyListFiltersAllY()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\SubCategory\GetList::create(
            [
                'isTaskCategory' => 'Y',
                'isDocCategory' => 'Y',
                'isScanCategory' => 'Y',
                'category' => 3011,
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.isTask = [[true]] AND '
            . 'm.isDoc = [[true]] AND '
            . 'm.isScan = [[true]] AND '
            . 'm.category = [[3011]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
