<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\DataGovUk
 */
class DataGovUkTest extends MockeryTestCase
{
    /** @var  m\MockInterface */
    private $mockStmt;
    /** @var  m\MockInterface */
    private $mockConn;

    /** @var DataGovUk */
    private $sut;

    public function setUp()
    {
        $this->mockStmt = m::mock(Statement::class)
            ->shouldReceive('execute')->once()->andReturn(true)
            ->getMock();

        $this->mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('close')->atMost()
            ->getMock();

        $this->sut = new DataGovUk($this->mockConn);
    }

    public function testFetchPsvOperatorList()
    {
        $this->mockConn
            ->shouldReceive('prepare')
            ->once()
            ->with(m::pattern('/data_gov_uk_psv_operator_list$/'))
            ->andReturn($this->mockStmt);

        static::assertEquals(
            $this->mockStmt,
            $this->sut->fetchPsvOperatorList()
        );
    }

    public function testFetchOperatorLicences()
    {
        $areas = ['areaKey1', 'areaKey2', 'areaKey3'];

        $this->mockStmt
            ->shouldReceive('bindValue')
            ->times(count($areas))
            ->with(m::anyOf(1, 2, 3), m::pattern('/^areaKey/'), \PDO::PARAM_STR);

        $this->mockConn
            ->shouldReceive('prepare')
            ->once()
            ->with(m::pattern('/data_gov_uk_operator_licence_view (.*)IN \(\?, \?, \?\)$/'))
            ->andReturn($this->mockStmt);

        static::assertEquals(
            $this->mockStmt,
            $this->sut->fetchOperatorLicences($areas)
        );
    }

    public function testBusRegisteredOnly()
    {
        $areas = ['areaKey1', 'areaKey2', 'areaKey3'];

        $this->mockStmt
            ->shouldReceive('bindValue')
            ->times(count($areas))
            ->with(m::anyOf(1, 2, 3), m::pattern('/^areaKey/'), \PDO::PARAM_STR);

        $this->mockConn
            ->shouldReceive('prepare')
            ->once()
            ->with(m::pattern('/data_gov_uk_bus_registered_only_view (.*)IN \(\?, \?, \?\)$/'))
            ->andReturn($this->mockStmt);

        static::assertEquals(
            $this->mockStmt,
            $this->sut->fetchBusRegisteredOnly($areas)
        );
    }

    public function testBusVariation()
    {
        $areas = ['areaKey1'];

        $this->mockStmt
            ->shouldReceive('bindValue')
            ->times(count($areas))
            ->with(1, 'areaKey1', \PDO::PARAM_STR);

        /** @var \Doctrine\DBAL\Connection $mockConn */
        $this->mockConn
            ->shouldReceive('prepare')
            ->once()
            ->with(m::pattern('/data_gov_uk_bus_variation_view (.*)IN \(\?\)$/'))
            ->andReturn($this->mockStmt);

        static::assertEquals(
            $this->mockStmt,
            $this->sut->fetchBusVariation($areas)
        );
    }
}
