<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Task as Repo;

/**
 * TaskTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TaskTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByOrganisation()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByIrfoOrganisation('ORG1'));

        $expectedQuery = 'BLAH AND m.irfoOrganisation = [[ORG1]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByTransportManager()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByTransportManager('TM1'));

        $expectedQuery = 'BLAH AND m.transportManager = [[TM1]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByUser()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByUser('U1'));

        $expectedQuery = 'BLAH AND m.assignedToUser = [[U1]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByUserWithOpenOnly()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByUser('U1', true));

        $expectedQuery = 'BLAH AND m.assignedToUser = [[U1]] AND m.isClosed = [[N]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForTmCaseDecision()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getSingleResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $case = 3;
        $transportManager = 4;

        $this->assertEquals(['RESULTS'], $this->sut->fetchForTmCaseDecision($case, $transportManager, 'subcat'));

        $expectedQuery =
            'BLAH AND m.transportManager = [[4]] AND m.case = [[3]] ' .
            'AND m.category = [[5]] AND m.subCategory = [[subcat]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
