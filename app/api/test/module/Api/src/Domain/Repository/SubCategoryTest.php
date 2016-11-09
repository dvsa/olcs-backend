<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query as TransferQry;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\SubCategory
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
            ->shouldReceive('order')->zeroOrMoreTimes()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $this->sut->shouldReceive('fetchPaginatedList')->andReturn('RESULTS');

        $dto = TransferQry\SubCategory\GetList::create([]);
        static::assertEquals('RESULTS', $this->sut->fetchList($dto));

        static::assertEquals('QUERY', $this->query);
    }

    public function testApplyListFiltersAllN()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC', [])->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $dto = TransferQry\SubCategory\GetList::create(
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
            ->shouldReceive('modifyQuery')->with($qb)->times(2)->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC', [])->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $dto = TransferQry\SubCategory\GetList::create(
            [
                'isTaskCategory' => 'Y',
                'isDocCategory' => 'Y',
                'isScanCategory' => 'Y',
                'category' => 3011,
            ]
        );
        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY ' .
            'SELECT DISTINCT m ' .
            'INNER JOIN ' . Entity\Doc\DocTemplate::class . ' dct '.
                'WITH (dct.category = m.category AND dct.subCategory = m.id) ' .
            'INNER JOIN ' . Entity\Doc\Document::class . ' dc WITH dc.id = dct.document ' .
            'AND m.isTask = [[true]] ' .
            'AND m.isDoc = [[true]] ' .
            'AND m.isScan = [[true]] ' .
            'AND m.category = [[3011]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
