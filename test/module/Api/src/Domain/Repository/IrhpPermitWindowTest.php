<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use DateTime;
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
}
