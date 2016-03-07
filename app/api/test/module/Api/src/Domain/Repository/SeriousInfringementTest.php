<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Comparison;
use Mockery as m;

/**
 * SeriousInfringementTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SeriousInfringementTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(SiRepo::class, true);
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
