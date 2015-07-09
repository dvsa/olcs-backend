<?php

/**
 * Cases test
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Transfer\Query\Cases\ByTransportManager;

/**
 * Cases test
 */
class CasesTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(CasesRepo::class);
    }

    /**
     * @param $qb
     * @return m\MockInterface
     */
    public function getMockRepo($qb)
    {
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        return $repo;
    }

    public function testApplyListFilters()
    {
        $sut = m::mock(CasesRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $transportManager = 3;

        $mockQuery = m::mock(ByTransportManager::class);
        $mockQuery->shouldReceive('getTransportManager')
            ->once()
            ->andReturn($transportManager)
            ->getMock();

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.transportManager', ':byTransportManager')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byTransportManager', $transportManager)->once()->andReturnSelf();

        $sut->applyListFilters($mockQb, $mockQuery);
    }
}
