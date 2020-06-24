<?php

/**
 * GetList test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\Address\GetList;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Address\GetList as Qry;
use Mockery as m;
use Dvsa\Olcs\Address\Service\AddressInterface;

/**
 * GetList test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new GetList();
        $this->mockedSmServices['AddressService'] = m::mock(AddressInterface::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['postcode' => 'ABC']);

        $this->mockedSmServices['AddressService']
            ->shouldReceive('fetchByPostcode')
            ->with('ABC')
            ->once()
            ->andReturn(['addresses'])
            ->getMock();

        $expected = [
            'result' => ['addresses'],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
