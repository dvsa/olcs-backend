<?php

/**
 * Fee Type repository test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as Repo;

/**
 * Fee Type repository test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTypeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class, true);
    }

    public function testFetchLatestForOverpayment()
    {
        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('setMaxResults')
            ->with(1)
            ->andReturnSelf();

        $qb->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                    ->shouldReceive('execute')
                    ->andReturn(['RESULTS'])
                    ->getMock()
            );

        $this->assertEquals('RESULTS', $this->sut->fetchLatestForOverpayment());

        $expectedQuery = 'QUERY AND ft.feeType = [[ADJUSTMENT]] ORDER BY ft.effectiveFrom DESC';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
