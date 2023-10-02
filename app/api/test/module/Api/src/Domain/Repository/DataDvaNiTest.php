<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Dvsa\Olcs\Api\Domain\Repository\DataDvaNi;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\DataDvaNi
 */
class DataDvaNiTest extends MockeryTestCase
{
    /** @var  m\MockInterface */
    private $mockStmt;
    /** @var  m\MockInterface */
    private $mockConn;

    /** @var DataDvaNi */
    private $sut;

    public function setUp(): void
    {
        $this->mockResult = m::mock(Result::class);

        $this->mockStmt = m::mock(Statement::class)
            ->expects('executeQuery')->andReturn($this->mockResult)
            ->getMock();

        $this->mockConn = m::mock(\Doctrine\DBAL\Connection::class)
            ->shouldReceive('close')->atMost()
            ->getMock();

        $this->sut = new DataDvaNi($this->mockConn);
    }

    public function testFetchNiOperatorLicences()
    {
        $this->mockConn
            ->shouldReceive('prepare')
            ->once()
            ->with('SELECT * FROM data_dva_ni_operator_licence_view')
            ->andReturn($this->mockStmt);

        static::assertEquals(
            $this->mockStmt,
            $this->sut->fetchNiOperatorLicences()
        );
    }
}
