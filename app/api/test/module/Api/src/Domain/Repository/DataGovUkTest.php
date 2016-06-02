<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\Repository\DataGovUk
 */
class DataGovUkTest extends MockeryTestCase
{
    public function test()
    {
        $expectStmt = 'Expect_PDOStatement';

        /** @var \Doctrine\DBAL\Connection $mockConn */
        $mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('query')
            ->once()
            ->with('/data_gov_uk_operator_licence_vw$/')
            ->andReturn($expectStmt)
            //
            ->shouldReceive('close')
            ->once()
            //
            ->getMock();

        static::assertEquals(
            $expectStmt,
            (new DataGovUk($mockConn))->fetchOperatorLicences()
        );
    }
}
