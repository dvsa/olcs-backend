<?php

/**
 * ApplicationOrganisationPersonTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOrganisationPerson as Repo;

/**
 * ApplicationOrganisationPersonTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationOrganisationPersonTest extends RepositoryTestCase
{
    /**
     * @var Repo
     */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchListForOrganisation()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefData')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('person', 'p')->once()->andReturnSelf()
            ->shouldReceive('with')->with('p.title')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchListForApplication(34));

        $expectedQuery = '[QUERY] AND m.application = [[34]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForApplicationAndPerson()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefData')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('person', 'p')->once()->andReturnSelf()
            ->shouldReceive('with')->with('p.title')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $this->assertEquals('RESULTS', $this->sut->fetchForApplicationAndPerson(34, 76));

        $expectedQuery = '[QUERY] AND m.application = [[34]] AND m.person = [[76]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForApplicationAndPersonNotFound()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefData')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('person', 'p')->once()->andReturnSelf()
            ->shouldReceive('with')->with('p.title')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([])
                ->getMock()
        );

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class);

        $this->sut->fetchForApplicationAndPerson(34, 76);
    }

    public function testFetchForApplicationAndOriginalPerson()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefData')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('person', 'p')->once()->andReturnSelf()
            ->shouldReceive('with')->with('p.title')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        $this->assertEquals('RESULTS', $this->sut->fetchForApplicationAndOriginalPerson(34, 76));

        $expectedQuery = '[QUERY] AND m.application = [[34]] AND m.originalPerson = [[76]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchForApplicationAndOriginalPersonNotFound()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf()
            ->shouldReceive('withRefData')->with()->once()->andReturnSelf()
            ->shouldReceive('with')->with('person', 'p')->once()->andReturnSelf()
            ->shouldReceive('with')->with('p.title')->once()->andReturnSelf();

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn([])
                ->getMock()
        );

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\NotFoundException::class);

        $this->sut->fetchForApplicationAndOriginalPerson(34, 76);
    }
}
