<?php

/**
 * Alert List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CompaniesHouse;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse\AlertList;
use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseAlert as AlertRepo;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as AlertEntity;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\AlertList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Alert List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class AlertListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AlertList();
        $this->mockRepo('CompaniesHouseAlert', AlertRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $alert1 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 1])->getMock();
        $alert2 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 2])->getMock();
        $mockList = [$alert1, $alert2];

        $this->repoMap['CompaniesHouseAlert']
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2)
            ->shouldReceive('getReasonValueOptions')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
            'count' => 2,
            'valueOptions' => [
                'companiesHouseAlertReason' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
