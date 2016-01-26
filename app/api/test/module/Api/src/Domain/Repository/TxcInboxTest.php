<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repo;

/**
 * TxcInboxTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TxcInboxTest extends RepositoryTestCase
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
        $this->assertEquals(['RESULTS'], $this->sut->fetchByOrganisation('ORG1'));

        $expectedQuery = 'BLAH AND m.organisation = [[ORG1]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForOrganisationByBusReg()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('busReg', 'b')->once()->andReturnSelf();

        $qb->shouldReceive('where')->with('b.id = :busReg')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForOrganisationByBusReg(2, 4));

        $expectedQuery = 'BLAH AND m.localAuthority IS NULL AND m.organisation = [[4]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLocalAuthorityByBusReg()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('busReg', 'b')->once()->andReturnSelf();

        $qb->shouldReceive('where')->with('b.id = :busReg')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLocalAuthorityByBusReg(2, 4));

        $expectedQuery = 'BLAH AND m.fileRead = 0 AND m.localAuthority = [[4]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLocalAuthorityByBusRegOperator()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('busReg', 'b')->once()->andReturnSelf();

        $qb->shouldReceive('where')->with('b.id = :busReg')->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLocalAuthorityByBusReg(2, null));

        $expectedQuery = 'BLAH AND m.localAuthority IS NULL';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
