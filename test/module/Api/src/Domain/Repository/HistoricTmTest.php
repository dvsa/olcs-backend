<?php

/**
 * HistoricTm repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use \Dvsa\Olcs\Api\Domain\Repository\HistoricTm as Repo;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Mockery as m;

/**
 * HistoricTm repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class HistoricTmTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('historicId', 3)
            ->andReturnSelf();

        $mockQ = m::mock(QueryInterface::class);
        $mockQ->shouldReceive('getHistoricId')
            ->andReturn(3);

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
