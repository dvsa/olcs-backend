<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repo;
use Dvsa\Olcs\Transfer\Query\Ebsr\SubmissionList as SubmissionListQry;
use \Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EbsrSubmissionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EbsrSubmissionTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByOrganisation()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);
        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $this->assertEquals(
            [
                'RESULTS'
            ], $this->sut->fetchByOrganisation(
                'ORG1',
                'submission_type',
                'submission_status'
            )
        );

        $expectedQuery = 'BLAH AND m.ebsrSubmissionType = [[submission_type]] AND e.ebsrSubmissionStatus = ' .
            '[[submission_status]] AND m.organisation = [[ORG1]]';
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
            ->with('m.organisation', ':organisation')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('organisation', 3)
            ->andReturnSelf();

        // status clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('m.ebsrSubmissionStatus')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('ebsrSubmissionStatus', 'foo')
            ->andReturnSelf();

        // subType clause
        $mockQb->shouldReceive('expr')
            ->andReturnSelf()
            ->shouldReceive('eq')
            ->with('m.ebsrSubmissionType')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with('ebsrSubmissionType', 'bar')
            ->andReturnSelf();

        $mockQ = m::mock(QueryInterface::class);
        $mockQ->shouldReceive('getOrganisation')
            ->andReturn(3)
            ->shouldReceive('getEbsrSubmissionType')
            ->andReturn('subType')
            ->shouldReceive('getStatus')
            ->andReturn('foo');

        $this->sut->applyListFilters($mockQb, $mockQ);
    }
}
