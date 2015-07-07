<?php

/**
 * ConditionUndertaking test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as Repo;

/**
 * ConditionUndertaking test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ConditionUndertakingTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchListForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefdata')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('attachedTo')->once()->andReturnSelf()
            ->shouldReceive('with')->with('conditionType')->once()->andReturnSelf()
            ->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf()
            ->shouldReceive('with')->with('oc.address')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLicence(95));

        $expectedQuery = 'BLAH AND m.licence = [[95]] AND m.isDraft = 0 AND m.isFulfilled = 0';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
