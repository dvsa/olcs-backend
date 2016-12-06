<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\BusRegBrowseView as Repo;

/**
 * BusRegSearchViewTest
 */
class BusRegBrowseViewTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchDistinctList()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchDistinctList('FIELD1'));

        $expectedQuery = 'BLAH DISTINCT SELECT m.FIELD1';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForExport()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('iterate')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $results = $this->sut->fetchForExport(
            ['FIELD1', 'FIELD2'],
            'DATE',
            ['TA1', 'TA2'],
            'STATUS'
        );
        $this->assertEquals(['RESULTS'], $results);

        $expectedQuery
            = 'BLAH SELECT m.FIELD1, m.FIELD2 '
            . 'AND m.acceptedDate = [[DATE]] '
            . 'AND m.trafficAreaId IN [[["TA1","TA2"]]] '
            . 'AND m.eventRegistrationStatus = [[STATUS]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForExportWithoutStatus()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('iterate')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $results = $this->sut->fetchForExport(
            ['FIELD1'],
            'DATE',
            ['TA1']
        );
        $this->assertEquals(['RESULTS'], $results);

        $expectedQuery
            = 'BLAH SELECT m.FIELD1 '
            . 'AND m.acceptedDate = [[DATE]] '
            . 'AND m.trafficAreaId IN [[["TA1"]]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
