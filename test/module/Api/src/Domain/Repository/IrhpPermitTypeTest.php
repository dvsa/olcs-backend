<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType;
use Mockery as m;
use DateTime;

/**
 * IRHP Permit Type test
 */
class IrhpPermitTypeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpPermitType::class);
    }

    public function testFetchAvailableTypes()
    {
        $now = new DateTime('2018-10-25 13:21:10');

        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchAvailableTypes($now));

        $expectedQuery = 'BLAH '
            . 'SELECT ipt, rd '
            . 'INNER JOIN ipt.name rd '
            . 'INNER JOIN ipt.irhpPermitStocks ips '
            . 'INNER JOIN ips.irhpPermitWindows ipw '
            . 'AND ipw.startDate <= [[2018-10-25T13:21:10+0000]] '
            . 'AND ipw.endDate > [[2018-10-25T13:21:10+0000]] '
            . 'ORDER BY rd.displayOrder ASC';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
