<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repo;
use Dvsa\Olcs\Transfer\Query\Ebsr\SubmissionList as SubmissionListQry;

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
}
