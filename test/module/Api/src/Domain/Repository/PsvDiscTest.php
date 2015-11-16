<?php

/**
 * Psv Disc test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\PsvDisc as PsvDiscRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Psv Disc test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDiscTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(PsvDiscRepo::class);
    }

    public function testFetchDiscsToPrint()
    {
        $licenceType = 'ltyp_r';

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('lta.isNi', 0)->once()->andReturn('condition1');
        $mockQb->shouldReceive('expr->eq')->with('llt.id', ':licenceType')->once()->andReturn('condition2');
        $mockQb->shouldReceive('expr->neq')
            ->with('lta.id', ':licenceTrafficAreaId')->once()->andReturn('condition3');
        $mockQb->shouldReceive('expr->eq')
            ->with('lgp.id', ':goodsOrPsv')->once()->andReturn('condition4');
        $mockQb->shouldReceive('expr->andX')
            ->with('condition1', 'condition2', 'condition3', 'condition4')->once()->andReturn('conditionAndX');

        $mockQb->shouldReceive('andWhere')->with('conditionAndX')->once()->andReturnSelf();

        $mockQb->shouldReceive('setParameter')
            ->with('licenceType', $licenceType)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('licenceTrafficAreaId', TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('goodsOrPsv', LicenceEntity::LICENCE_CATEGORY_PSV)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('expr->isNull')->with('psv.ceasedDate')->once()->andReturn('noCeasedDateCond');
        $mockQb->shouldReceive('expr->isNull')->with('psv.issuedDate')->once()->andReturn('noIssuedDateCond');
        $mockQb->shouldReceive('andWhere')->with('noCeasedDateCond')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->with('noIssuedDateCond')->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.trafficArea', 'lta')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.licenceType', 'llt')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('l.goodsOrPsv', 'lgp')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('psv')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->fetchDiscsToPrint($licenceType);
    }

    public function testSetPrintingOn()
    {
        $discs = ['d1', 'd2'];
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with('Y', $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOn($discs));
    }

    public function testSetPrintingOff()
    {
        $discs = ['d1', 'd2'];
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with('N', $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOff($discs));
    }

    public function testSetPrinting()
    {
        $type = 'Y';
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $mockFetched = m::mock()
            ->shouldReceive('setIsPrinting')
            ->with($type)
            ->once()
            ->getMock();

        $sut->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockFetched)
            ->once()
            ->shouldReceive('save')
            ->with($mockFetched)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrinting($type, [$mockDisc]));
    }

    public function testSetIsPrintingOffAndAssignNumbers()
    {
        $type = 'N';
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $mockFetched = m::mock()
            ->shouldReceive('setIsPrinting')
            ->with($type)
            ->once()
            ->shouldReceive('setDiscNo')
            ->with(1)
            ->once()
            ->shouldReceive('setIssuedDate')
            ->with(m::type(DateTime::class))
            ->once()
            ->getMock();

        $sut->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockFetched)
            ->once()
            ->shouldReceive('save')
            ->with($mockFetched)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOffAndAssignNumbers([$mockDisc], 1));
    }

    public function testApplyListFiltersIncludeCeasedFalse()
    {
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $qb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $query = \Dvsa\Olcs\Transfer\Query\Licence\PsvDiscs::create(['includeCeased' => false, 'id' => 12]);

        $sut->shouldReceive('createQueryBuilder')->with()->once()->andReturn($qb);
        $sut->shouldReceive('buildDefaultListQuery')->with($qb, $query)->once();
        $sut->shouldReceive('applyListJoins')->with($qb)->once();
        $sut->shouldReceive('fetchPaginatedList')->with($qb, \Doctrine\ORM\Query::HYDRATE_ARRAY)->once();

        $qb->shouldReceive('expr->isNull')->with('psv.ceasedDate')->once()->andReturn('QUERY1');
        $qb->shouldReceive('andWhere')->with('QUERY1')->once();

        $qb->shouldReceive('expr->eq')->with('psv.licence', ':licence')->once()->andReturn('QUERY2');
        $qb->shouldReceive('andWhere')->with('QUERY2')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', 12)->once();

        $sut->fetchList($query);
    }

    public function testApplyListFiltersIncludeCeasedTrue()
    {
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $qb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $query = \Dvsa\Olcs\Transfer\Query\Licence\PsvDiscs::create(['includeCeased' => true, 'id' => 12]);

        $sut->shouldReceive('createQueryBuilder')->with()->once()->andReturn($qb);
        $sut->shouldReceive('buildDefaultListQuery')->with($qb, $query)->once();
        $sut->shouldReceive('applyListJoins')->with($qb)->once();
        $sut->shouldReceive('fetchPaginatedList')->with($qb, \Doctrine\ORM\Query::HYDRATE_ARRAY)->once();

        $qb->shouldReceive('expr->eq')->with('psv.licence', ':licence')->once()->andReturn('QUERY2');
        $qb->shouldReceive('andWhere')->with('QUERY2')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licence', 12)->once();

        $sut->fetchList($query);
    }
}
