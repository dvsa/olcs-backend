<?php

/**
 * Decision repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Repository\Decision as Repo;
use Dvsa\Olcs\Transfer\Query\Decision\DecisionList;

/**
 * Decision repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class DecisionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testApplyListFilters()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $goodsOrPsv = 'lcat_psv';
        $isNi = 'Y';

        $query = DecisionList::create(['isNi' => $isNi, 'goodsOrPsv' => $goodsOrPsv]);

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.isNi', ':isNi')->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('m.goodsOrPsv', ':goodsOrPsv')->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('isNi', $isNi)->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('goodsOrPsv', $goodsOrPsv)->andReturnSelf();

        $sut->applyListFilters($mockQb, $query);

    }
}
