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
use Doctrine\DBAL\Connection;

/**
 * Psv Disc test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDiscTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->activeStatuses = [
            LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
            LicenceEntity::LICENCE_STATUS_GRANTED,
            LicenceEntity::LICENCE_STATUS_VALID,
            LicenceEntity::LICENCE_STATUS_CURTAILED,
            LicenceEntity::LICENCE_STATUS_SUSPENDED
        ];
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
        $mockQb->shouldReceive('expr->in')->with('l.status', ':activeStatuses')->once()->andReturn('activeStatuses');
        $mockQb->shouldReceive('andWhere')->with('activeStatuses')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('activeStatuses', $this->activeStatuses)
            ->once()
            ->andReturnSelf();

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
        $discs = [1, 2];
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with(1, $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOn($discs));
    }

    public function testSetPrintingOff()
    {
        $discs = [1, 2];
        $sut = m::mock(PsvDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with(0, $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOff($discs));
    }

    public function testSetPrinting()
    {
        $this->expectUpdateWithData(
            'Discs\PsvDiscsSetIsPrinting',
            ['isPrinting' => 1, 'ids' => [1, 2]],
            ['isPrinting' => \PDO::PARAM_INT, 'ids' => Connection::PARAM_INT_ARRAY]
        );

        $this->sut->setIsPrintingOn([1, 2]);
    }

    public function testSetIsPrintingOffAndAssignNumbers()
    {
        $this->expectUpdateWithData(
            'Discs\PsvDiscsSetIsPrintingOffAndDiscNo',
            ['ids' => [1, 2], 'startNumber' => 1],
            ['ids' => Connection::PARAM_INT_ARRAY, 'startNumber' => \PDO::PARAM_INT]
        );

        $this->sut->setIsPrintingOffAndAssignNumbers([1, 2], 1);
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
        $qb->shouldReceive('setParameter')->with('licence', 12)->once()->andReturnSelf();
        $qb->shouldReceive('addSelect')->with('psv.discNo+0 as HIDDEN intDiscNo')->once()->andReturnSelf();
        $qb->shouldReceive('orderBy')->with('intDiscNo', 'ASC')->once()->andReturnSelf();

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
        $qb->shouldReceive('setParameter')->with('licence', 12)->once()->andReturnSelf();
        $qb->shouldReceive('addSelect')->with('psv.discNo+0 as HIDDEN intDiscNo')->once()->andReturnSelf();
        $qb->shouldReceive('orderBy')->with('intDiscNo', 'ASC')->once()->andReturnSelf();

        $sut->fetchList($query);
    }

    public function testCeaseDiscsForLicence()
    {
        $licenceId = 123;

        $this->expectQueryWithData('Discs\CeaseDiscsForLicence', ['licence' => 123]);

        $this->sut->ceaseDiscsForLicence($licenceId);
    }

    public function testFetchDiscsToPrintMin()
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
        $mockQb->shouldReceive('expr->in')->with('l.status', ':activeStatuses')->once()->andReturn('activeStatuses');
        $mockQb->shouldReceive('andWhere')->with('activeStatuses')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('activeStatuses', $this->activeStatuses)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('leftJoin')->with('psv.licence', 'l')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('l.trafficArea', 'lta')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('l.licenceType', 'llt')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('l.goodsOrPsv', 'lgp')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('psv')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->fetchDiscsToPrintMin($licenceType);
    }
}
