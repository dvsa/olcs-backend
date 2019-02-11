<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Mockery as m;

/**
 * IRHP Permit Application test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IrhpPermitApplicationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitApplication::class);
    }

    public function testGetByIrhpApplicationWithStockInfo()
    {
        $irhpApplicationId = 7;

        $expectedResult = [
            [
                'irhpPermitApplication' => m::mock(IrhpPermitApplicationEntity::class),
                'validTo' => '2019-12-31',
                'countryId' => 'ES'
            ],
            [
                'irhpPermitApplication' => m::mock(IrhpPermitApplicationEntity::class),
                'validTo' => '2018-12-31',
                'countryId' => 'IT'
            ],
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with(
                'ipa as irhpPermitApplication, ips.validTo as validTo, ips.id as stockId, ' .
                'IDENTITY(ips.country) as countryId'
            )
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(IrhpPermitApplicationEntity::class, 'ipa')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipa.irhpPermitWindow', 'ipw')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('innerJoin')
            ->with('ipw.irhpPermitStock', 'ips')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('IDENTITY(ipa.irhpApplication) = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $irhpApplicationId)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getResult')
            ->once()
            ->andReturn($expectedResult);

        $this->assertEquals(
            $expectedResult,
            $this->sut->getByIrhpApplicationWithStockInfo($irhpApplicationId)
        );
    }
}
