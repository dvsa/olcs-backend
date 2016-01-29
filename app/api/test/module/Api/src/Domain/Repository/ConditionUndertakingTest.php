<?php

/**
 * ConditionUndertaking test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as Repo;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;

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

    public function testFetchListForLicenceReadOnly()
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
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLicenceReadOnly(95));

        $expectedQuery = 'BLAH AND m.licence = [[95]] AND m.isDraft = 0 AND m.isFulfilled = 0';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForApplication()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('attachedTo')->once()->andReturnSelf()
            ->shouldReceive('with')->with('conditionType')->once()->andReturnSelf()
            ->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf()
            ->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf()
            ->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf()
            ->shouldReceive('with')->with('addedVia')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForApplication(95));

        $expectedQuery = 'BLAH AND m.application = [[95]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForVariation()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('attachedTo')->once()->andReturnSelf()
            ->shouldReceive('with')->with('conditionType')->once()->andReturnSelf()
            ->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf()
            ->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf()
            ->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf()
            ->shouldReceive('with')->with('licConditionVariation')->once()->andReturnSelf()
            ->shouldReceive('with')->with('addedVia')->once()->andReturnSelf()
            ->shouldReceive('order')->with('id', 'ASC')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForVariation(95, 33));

        $expectedQuery = 'BLAH AND m.application = [[95]] OR m.licence = [[33]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLicence()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('attachedTo')->once()->andReturnSelf()
            ->shouldReceive('with')->with('conditionType')->once()->andReturnSelf()
            ->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf()
            ->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf()
            ->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf()
            ->shouldReceive('with')->with('addedVia')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLicence(95));

        $expectedQuery = 'BLAH AND m.licence = [[95]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForS4()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForS4(95));

        $expectedQuery = 'BLAH AND m.s4 = [[95]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLicenceAndConditionType()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('with')->with('attachedTo')->once()->andReturnSelf()
            ->shouldReceive('with')->with('conditionType')->once()->andReturnSelf()
            ->shouldReceive('with')->with('operatingCentre', 'oc')->once()->andReturnSelf()
            ->shouldReceive('with')->with('oc.address', 'add')->once()->andReturnSelf()
            ->shouldReceive('with')->with('add.countryCode')->once()->andReturnSelf()
            ->shouldReceive('with')->with('addedVia')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(
            ['RESULTS'],
            $this->sut->fetchListForLicence(95, ConditionUndertakingEntity::TYPE_CONDITION)
        );

        $expectedQuery
            = 'BLAH AND m.licence = [[95]] AND m.conditionType = [['.ConditionUndertakingEntity::TYPE_CONDITION.']]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListForLicConditionVariation()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForLicConditionVariation(95));

        $expectedQuery = 'BLAH AND m.licConditionVariation = [[95]]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
