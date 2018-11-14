<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Mockery as m;

/**
 * IRHP Permit Stock test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class IrhpPermitStockTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitStock::class);
    }

    public function testGetNextIrhpPermitStockByPermitType()
    {
        $date = new DateTime('2010-01-01');
        $permitType = 'permit_ecmt';
        $expectedResult = m::mock(IrhpPermitStockEntity::class);

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $gteFunc = m::mock(Func::class);
        $eqFunc = m::mock(Func::class);
        $andXFunc = m::mock(Func::class);

        $expr = m::mock(Expr::class);

        $queryBuilder->shouldReceive('expr')
            ->andReturn($expr);

        $expr->shouldReceive('andX')
            ->with($gteFunc, $eqFunc)
            ->once()
            ->andReturn($andXFunc);

        $expr->shouldReceive('gte')
            ->with('ips.validFrom', '?1')
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
            ->shouldReceive('setParameter')
            ->with(1, $date)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $permitType)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ips.validTo', 'ASC')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn([$expectedResult]);

            $this->assertEquals(
                $expectedResult,
                $this->sut->getNextIrhpPermitStockByPermitType($permitType, $date, Query::HYDRATE_ARRAY)
            );
    }
}
