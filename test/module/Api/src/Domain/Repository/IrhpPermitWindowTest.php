<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
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

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $betweenFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);
        $expr->shouldReceive('between')
            ->with('?1', 'ipw.startDate', 'ipw.endDate')
            ->once()
            ->andReturn($betweenFunc);

        $queryBuilder->shouldReceive('expr')
            ->once()
            ->andReturn($expr);

        $queryBuilder->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('add')
            ->with('where', $betweenFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->with(Query::HYDRATE_ARRAY)
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchOpenWindows($dateTime)
        );
    }

    public function testFetchLastOpenWindow()
    {
        $expectedResult = m::mock(IrhpPermitWindowEntity::class);

        $dateTime = m::mock(DateTime::class);

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $ltFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);
        $expr->shouldReceive('lt')
            ->with('ipw.endDate', '?1')
            ->once()
            ->andReturn($ltFunc);

        $queryBuilder->shouldReceive('expr')
            ->once()
            ->andReturn($expr);

        $queryBuilder->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('add')
            ->with('where', $ltFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ipw.endDate', 'DESC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $dateTime)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchLastOpenWindow($dateTime)
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
            . 'AND m.endDate >= [[2018-10-23T00:00:00+0000]] '
            . 'AND m.endDate < [[2018-10-25T13:21:10+0000]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
