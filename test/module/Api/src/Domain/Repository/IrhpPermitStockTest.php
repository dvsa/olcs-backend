<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

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
        $expectedResult = [0 => 'result'];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

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
            ->with('ips.validFrom >= ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('ipt.name = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('ips.validTo', 'ASC')
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
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->andReturnSelf()
            ->shouldReceive('getQuery->getOneOrNullResult')
            ->once()
            ->andReturn($expectedResult);

            $this->assertEquals(
                $expectedResult,
                $this->sut->getNextIrhpPermitStockByPermitType($permitType, $date)
            );
    }
}
