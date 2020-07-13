<?php

/**
 * GetAddress test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\Address\GetAddress;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Address\GetAddress as Qry;
use Mockery as m;
use Dvsa\Olcs\Address\Service\AddressInterface;

/**
 * GetAddress test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetAddressTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new GetAddress();
        $this->mockedSmServices['AddressService'] = m::mock(AddressInterface::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['uprn' => 123]);

        $this->mockedSmServices['AddressService']
            ->shouldReceive('fetchByUprn')
            ->with(123)
            ->once()
            ->andReturn('address')
            ->getMock();

        $expected = [
            'result' => ['address'],
            'count' => 1
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
