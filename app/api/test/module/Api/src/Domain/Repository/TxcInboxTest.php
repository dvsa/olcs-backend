<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Query\Bus\TxcInboxList;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repo;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;

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

    public function testBuildDefaultQuery()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $mockQb->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $mockQb->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('m.busReg', 'b')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('b.ebsrSubmissions', 'e')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('b.licence', 'l')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('b.otherServices')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('l.organisation')->once()->andReturnSelf();

        $sut->buildDefaultListQuery($mockQb, $mockQi);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $mockQb = m::mock(QueryBuilder::class);

        // organisation clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('m.localAuthority', ':localAuthority')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('localAuthority', 3)
            ->andReturnSelf();

        // status clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('b.status', ':status')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('status', 'foo')
            ->andReturnSelf();

        // subType clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('e.ebsrSubmissionType', ':ebsrSubmissionType')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('ebsrSubmissionType', 'bar')
            ->andReturnSelf();

        // fileRead clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('m.fileRead', '0')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf();

        $query = TxcInboxList::create(['localAuthority' => 3, 'subType' => 'bar', 'status' => 'foo']);

        $this->sut->applyListFilters($mockQb, $query);
    }

    public function testFetchLinkedToDocument()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('where')->with('m.zipDocument = :documentId')->andReturnSelf();
        $qb->shouldReceive('where')->with('m.pdfDocument = :documentId')->andReturnSelf();
        $qb->shouldReceive('where')->with('m.routeDocument = :documentId')->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchLinkedToDocument(23));

        $expectedQuery = 'BLAH OR m.zipDocument = [[23]] OR m.routeDocument = [[23]] OR m.pdfDocument = [[23]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByIdsForLocalAuthority()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByIdsForLocalAuthority([2], 4));

        $expectedQuery = 'BLAH AND m.localAuthority = [[4]] AND m.id IN [2]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByIdsForLocalAuthorityWithEmptyData()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([])
                ->getMock()
        );
        $this->assertEquals([], $this->sut->fetchByIdsForLocalAuthority([], 4));
    }
}
