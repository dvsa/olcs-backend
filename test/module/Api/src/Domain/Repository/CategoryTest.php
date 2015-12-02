<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

/**
 * CategoryTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CategoryTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\Category::class, true);
    }

    public function testApplyListFiltersNoFilters()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\Category\GetList::create(
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
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\Category\GetList::create(
            [
                'isTaskCategory' => 'N',
                'isDocCategory' => 'N',
                'isScanCategory' => 'N',
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.isTaskCategory = [[false]] AND '
            . 'm.isDocCategory = [[false]] AND '
            . 'm.isScanCategory = [[false]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testApplyListFiltersAllY()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')
            ->with($qb)
            ->andReturnSelf()
            ->shouldReceive('withRefdata')
            ->once()
            ->andReturnSelf();

        $dto = \Dvsa\Olcs\Transfer\Query\Category\GetList::create(
            [
                'isTaskCategory' => 'Y',
                'isDocCategory' => 'Y',
                'isScanCategory' => 'Y',
            ]
        );
        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY AND m.isTaskCategory = [[true]] AND '
            . 'm.isDocCategory = [[true]] AND '
            . 'm.isScanCategory = [[true]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
