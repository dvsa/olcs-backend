<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Mockery as m;

/**
 * IRHP Permit Window test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitWindowTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitWindow::class);
    }

    public function testFetchOpenWindows()
    {
        $expectedResult = [
            m::mock(IrhpPermitWindowEntity::class),
            m::mock(IrhpPermitWindowEntity::class),
        ];

        $dateTime = m::mock(DateTime::class);

        $irhpPermitStock = 1;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $andXFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $betweenFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($eqFunc, $betweenFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('eq')
            ->with('?1', 'ipw.irhpPermitStock')
            ->once()
            ->andReturn($eqFunc)
            ->shouldReceive('between')
            ->with('?2', 'ipw.startDate', 'ipw.endDate')
            ->once()
            ->andReturn($betweenFunc);

        $queryBuilder->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $irhpPermitStock)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->with(Query::HYDRATE_ARRAY)
            ->andReturn($expectedResult);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($queryBuilder)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchOpenWindows($irhpPermitStock, $dateTime)
        );
    }

    public function testFetchLastOpenWindow()
    {
        $expectedResult = m::mock(IrhpPermitWindowEntity::class);

        $dateTime = m::mock(DateTime::class);

        $irhpPermitStock = 1;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $andXFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $gtFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($eqFunc, $gtFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('eq')
            ->with('?1', 'ipw.irhpPermitStock')
            ->once()
            ->andReturn($eqFunc)
            ->shouldReceive('gt')
            ->with('?2', 'ipw.endDate')
            ->once()
            ->andReturn($gtFunc);

        $queryBuilder->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ipw.endDate', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $irhpPermitStock)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn($expectedResult);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($queryBuilder)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchLastOpenWindow($irhpPermitStock, $dateTime)
        );
    }

    public function testFetchLastOpenWindowByStockId()
    {
        $expectedResult = m::mock(IrhpPermitWindowEntity::class);

        $irhpPermitStockId = 1;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $betweenFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $andXFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($betweenFunc, $eqFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('between')
            ->with('?1', 'ipw.startDate', 'ipw.endDate')
            ->once()
            ->andReturn($betweenFunc)
            ->shouldReceive('eq')
            ->with('ipw.irhpPermitStock', '?2')
            ->once()
            ->andReturn($eqFunc);

        $queryBuilder->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ipw.id', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, m::type('DateTime'))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $irhpPermitStockId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn([$expectedResult]);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchLastOpenWindowByStockId($irhpPermitStockId, Query::HYDRATE_ARRAY)
        );
    }

    public function testFetchWindowsToBeClosed()
    {
        $now = new \DateTime('2018-10-25 13:21:10');

        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchWindowsToBeClosed($now, '-2 days'));

        $expectedQuery = 'BLAH '
            . 'AND ipw.endDate >= [[2018-10-23T00:00:00+0000]] '
            . 'AND ipw.endDate < [[2018-10-25T13:21:10+0000]]';

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchOpenWindowsByCountry()
    {
        $now = new \DateTime('2018-10-25 13:21:10');

        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(
            ['RESULTS'],
            $this->sut->fetchOpenWindowsByCountry(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                ['DE', 'NL'],
                $now
            )
        );

        $expectedQuery = 'BLAH '
            . 'SELECT ipw DISTINCT '
            . 'INNER JOIN ipw.irhpPermitStock ips '
            . 'INNER JOIN ips.irhpPermitType ipt '
            . 'INNER JOIN ips.country c '
            . 'AND ipt.id = [['.IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL.']] '
            . 'AND ipw.startDate <= [[2018-10-25T13:21:10+0000]] '
            . 'AND ipw.endDate > [[2018-10-25T13:21:10+0000]] '
            . 'AND c.id IN [[["DE","NL"]]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
