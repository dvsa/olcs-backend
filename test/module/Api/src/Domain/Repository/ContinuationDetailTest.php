<?php

/**
 * ContinuationDetailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as Repo;

/**
 * ContinuationDetailTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationDetailTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('licence', 'l')->once()->andReturnSelf()
            ->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchForLicence(95));

        $dateTime = new \Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime();
        $year = $dateTime->format('Y');
        $futureYear = $year + 4;
        $month = $dateTime->format('n');

        $expectedQuery = <<<EOT
BLAH AND m.licence = [[95]]
    AND l.status IN [[["lsts_valid","lsts_curtailed","lsts_suspended"]]]
    AND (c.month >= [[$month]] AND c.year = [[$year]])
        OR (c.year > [[$year]] AND c.year < [[$futureYear]])
        OR (c.month <= [[$month]] AND c.year = [[$futureYear]])
    AND m.status IN ([[["con_det_sts_printed","con_det_sts_acceptable","con_det_sts_unacceptable"]]])
        OR (m.status = 'con_det_sts_complete' AND m.received = 'N')
EOT;
        // Expected query has be formatted to make it readable, need to make it non formatted for assertion
        // remove new lines
        $expectedQuery = str_replace("\n", ' ', $expectedQuery);
        // remove indentation
        $expectedQuery = str_replace("  ", '', $expectedQuery);

        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchOngoingForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('continuation', 'c')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getSingleResult')
                ->andReturn('RESULT')
                ->getMock()
        );
        $this->assertEquals('RESULT', $this->sut->fetchOngoingForLicence(95));

        $expectedQuery = 'BLAH AND m.licence = [[95]] AND m.status = [[con_det_sts_acceptable]]';

        $this->assertEquals($expectedQuery, $this->query);
    }
}
