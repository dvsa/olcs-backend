<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetList;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetListByIrhpApplication;
use Mockery as m;

/**
 * IRHP Candidate Permit test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpCandidatePermitTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpCandidatePermit::class);
    }

    public function testFetchListForGetListByIrhpApplication()
    {
        $irhpApplicationId = 10;

        $this->setUpSut(IrhpCandidatePermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf()
            ->shouldReceive('order')->once()->andReturnSelf();

        $query = GetListByIrhpApplication::create(
            [
                'irhpApplication' => $irhpApplicationId,
                'page' => 1,
                'limit' => 25,
                'order' => 'id',
                'sort' => 'ASC',
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND m.successful = [[true]] '
            . 'AND ia.status = [['.RefData::PERMIT_APP_STATUS_AWAITING_FEE.']] '
            . 'AND ipa.irhpApplication = [['.$irhpApplicationId.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForGetListByIrhpApplicationPreGrant()
    {
        $irhpApplicationId = 10;

        $this->setUpSut(IrhpCandidatePermit::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('with')->with('irhpPermitApplication', 'ipa')->once()->andReturnSelf()
            ->shouldReceive('with')->with('ipa.irhpApplication', 'ia')->once()->andReturnSelf()
            ->shouldReceive('paginate')->once()->andReturnSelf()
            ->shouldReceive('order')->once()->andReturnSelf();

        $query = GetListByIrhpApplication::create(
            [
                'irhpApplication' => $irhpApplicationId,
                'page' => 1,
                'limit' => 25,
                'order' => 'id',
                'sort' => 'ASC',
                'isPreGrant' => true
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND ia.status IN [[["'.RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION.'"]]] '
            . 'AND ipa.irhpApplication = [['.$irhpApplicationId.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    /**
     * @dataProvider dpFetchCountInRangeWhereApplicationAwaitingFee
     */
    public function testFetchCountInRangeWhereApplicationAwaitingFee($countInRange, $expectedResult)
    {
        $rangeId = 22;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(icp.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpApplication', 'ia')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(icp.irhpPermitRange) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ia.status = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $rangeId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, IrhpInterface::STATUS_AWAITING_FEE)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($countInRange);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchCountInRangeWhereApplicationAwaitingFee($rangeId)
        );
    }

    public function dpFetchCountInRangeWhereApplicationAwaitingFee()
    {
        return [
            [null, 0],
            [42, 42]
        ];
    }

    /**
     * @dataProvider dpFetchCountInStockWhereApplicationAwaitingFee
     */
    public function testFetchCountInStockWhereApplicationAwaitingFee($emissionsCategoryId, $countInStock, $expectedResult)
    {
        $stockId = 22;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with('count(icp.id)')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpCandidatePermitEntity::class, 'icp')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitApplication', 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('icp.irhpPermitRange', 'ipr')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpApplication', 'ia')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipr.irhpPermitStock) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ia.status = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('IDENTITY(ipr.emissionsCategory) = ?3')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $stockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, IrhpInterface::STATUS_AWAITING_FEE)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(3, $emissionsCategoryId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleScalarResult')
            ->once()
            ->andReturn($countInStock);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchCountInStockWhereApplicationAwaitingFee($stockId, $emissionsCategoryId)
        );
    }

    public function dpFetchCountInStockWhereApplicationAwaitingFee()
    {
        return [
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, null, 0],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, null, 0],
            [null, null, 0],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, 20, 20],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, 20, 20],
            [null, 20, 20]
        ];
    }
}
