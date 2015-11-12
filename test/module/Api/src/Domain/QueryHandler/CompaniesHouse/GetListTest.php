<?php

/**
 * Get List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CompaniesHouse;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse\GetList;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\GetList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Get List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GetList();
        $this->mockCh = m::mock(\Dvsa\Olcs\Api\Service\CompaniesHouseService::class);
        $this->mockedSmServices = [
            'CompaniesHouseService' => $this->mockCh
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = [
            'type' => 'CompanyDetails',
            'value' => '12345678'
        ];
        $query = Qry::create($data);

        $this->mockCh
            ->shouldReceive('getList')
            ->with($data)
            ->andReturn(['Results' => ['company'], 'Count' => 1])
            ->once();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                'company'
            ],
            'count' => 1,
            'count-unfiltered' => 1
        ];

        $this->assertEquals($expected, $result);
    }
}
