<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\Repository\DataGovUk
 */
class DataGovUkTest extends MockeryTestCase
{
    public function testFetchOperatorLicences()
    {
        $areas = ['areaKey1', 'areaKey2', 'areaKey3'];

        $mockStmt = m::mock(Statement::class)
            ->shouldReceive('bindValue')
            ->times(count($areas))
            ->with(m::anyOf(1, 2, 3), '/^areaKey/', \PDO::PARAM_STR)
            //
            ->shouldReceive('execute')->once()->andReturn(true)
            ->getMock();

        /** @var \Doctrine\DBAL\Connection $mockConn */
        $mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('prepare')
            ->once()
            ->with('/data_gov_uk_operator_licence_view (.*)IN \(\?, \?, \?\)$/')
            ->andReturn($mockStmt)
            //
            ->shouldReceive('close')->once()
            //
            ->getMock();

        static::assertEquals(
            $mockStmt,
            (new DataGovUk($mockConn))->fetchOperatorLicences($areas)
        );
    }

    public function testBusRegisteredOnly()
    {
        $areas = ['areaKey1', 'areaKey2', 'areaKey3'];

        $mockStmt = m::mock(Statement::class)
            ->shouldReceive('bindValue')
            ->times(count($areas))
            ->with(m::anyOf(1, 2, 3), '/^areaKey/', \PDO::PARAM_STR)
            //
            ->shouldReceive('execute')->once()->andReturn(true)
            ->getMock();

        /** @var \Doctrine\DBAL\Connection $mockConn */
        $mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('prepare')
            ->once()
            ->with('/data_gov_uk_bus_registered_only_view (.*)IN \(\?, \?, \?\)$/')
            ->andReturn($mockStmt)
            //
            ->shouldReceive('close')->once()
            //
            ->getMock();

        static::assertEquals(
            $mockStmt,
            (new DataGovUk($mockConn))->fetchBusRegisteredOnly($areas)
        );
    }
}
