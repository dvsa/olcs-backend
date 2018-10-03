<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
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

    public function testFetchLastOpenWindowByPermitType()
    {
        $expectedResult = m::mock(IrhpPermitWindowEntity::class);
        $expectedStock = m::mock(IrhpPermitStockEntity::class);

        $irhpPermitType = EcmtPermitApplication::PERMIT_TYPE;

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $lteFunc = m::mock(Func::class);
        $gteFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $andXFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($lteFunc, $gteFunc, $eqFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('lte')
            ->with('ips.validFrom', '?1')
            ->once()
            ->andReturn($lteFunc)
            ->shouldReceive('gte')
            ->with('ips.validTo', '?1')
            ->once()
            ->andReturn($gteFunc)
            ->shouldReceive('eq')
            ->with('ipt.name', '?2')
            ->once()
            ->andReturn($eqFunc);

        $queryBuilder->shouldReceive('select')
            ->with('ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitStockEntity::class, 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ips.irhpPermitType', 'ipt')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ips.validTo', 'ASC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, m::type('DateTime'))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $irhpPermitType)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn($expectedStock);

        $queryBuilder1 = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder1);

        $irhpPermitStockId = 1;
        $expectedStock->shouldReceive('getId')
            ->once()
            ->andReturn($irhpPermitStockId);

        $betweenFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $andXFunc1 = m::mock(Func::class);

        $expr1 = m::mock(Expr::class);

        $queryBuilder1->shouldReceive('expr')
            ->andReturn($expr1);

        $expr1->shouldReceive('andX')
            ->with($betweenFunc, $eqFunc)
            ->once()
            ->andReturn($andXFunc1);

        $expr1->shouldReceive('between')
            ->with('?1', 'ipw.startDate', 'ipw.endDate')
            ->once()
            ->andReturn($betweenFunc)
            ->shouldReceive('eq')
            ->with('ipw.irhpPermitStock', '?2')
            ->once()
            ->andReturn($eqFunc);

        $queryBuilder1->shouldReceive('select')
            ->with('ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitWindowEntity::class, 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with($andXFunc1)
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
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->fetchLastOpenWindowByPermitType($irhpPermitType)
        );
    }
}
