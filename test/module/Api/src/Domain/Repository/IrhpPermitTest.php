<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Mockery as m;

/**
 * IRHP Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermit::class);
    }

    public function testGetPermitCount()
    {
        $permitCount = 744;
        $stockId = 5;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(ip.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitEntity::class, 'ip')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ip.irhpPermitRange', 'ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($permitCount);

        $this->assertEquals(
            $permitCount,
            $this->sut->getPermitCount($stockId)
        );
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(IrhpPermit::class, true);
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $mockQ = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('ipa.ecmtPermitApplication', ':ecmtId')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('ecmtId', 1)
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('m.permitNumber', 'DESC')
            ->andReturnSelf();

        $this->sut->applyListFilters($mockQb, $mockQ);
    }

    public function testApplyListJoins()
    {
        $sut = m::mock(IrhpPermit::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('modifyQuery')->andReturnSelf();
        $mockQb->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf();
        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);
        $sut->applyListJoins($mockQb);
    }
}
