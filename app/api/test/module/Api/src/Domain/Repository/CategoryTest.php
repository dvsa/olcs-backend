<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use \Dvsa\Olcs\Transfer\Query as TransferQry;
use Dvsa\Olcs\Api\Entity;
 use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\Category
 */
class CategoryTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(\Dvsa\Olcs\Api\Domain\Repository\Category::class, true);
    }

    public function testApplyListFiltersInvalidClass()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->times(1)->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $dto = TransferQry\Category\GetList::create([]);

        static::assertEquals('RESULTS', $this->sut->fetchList($dto));
        static::assertEquals('QUERY', $this->query);
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

        $dto = TransferQry\Category\GetList::create(
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
            ->shouldReceive('modifyQuery')->times(2)->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf();

        $this->sut->shouldReceive('fetchPaginatedList')
            ->andReturn('RESULTS');

        $dto = TransferQry\Category\GetList::create(
            [
                'isTaskCategory' => 'Y',
                'isDocCategory' => 'Y',
                'isScanCategory' => 'Y',
            ]
        );
        $this->assertEquals('RESULTS', $this->sut->fetchList($dto));

        $expectedQuery = 'QUERY '.
            'SELECT DISTINCT m ' .
            'INNER JOIN ' . Entity\Doc\DocTemplate::class . ' dct WITH dct.category = m.id ' .
            'INNER JOIN ' . Entity\Doc\Document::class . ' dc WITH dc.id = dct.document ' .
            'AND m.isTaskCategory = [[true]] ' .
            'AND m.isDocCategory = [[true]] ' .
            'AND m.isScanCategory = [[true]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
