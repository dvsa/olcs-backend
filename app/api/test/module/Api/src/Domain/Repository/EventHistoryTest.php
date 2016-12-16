<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\EventHistory as Repo;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EventHistoryTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EventHistoryTest extends RepositoryTestCase
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

    public function testFetchByAccount()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByAccount('USER', 'EHT', 'SORT', 'ORDER', 1));

        $expectedQuery = 'BLAH AND m.account = [[USER]] AND m.eventHistoryType = [[EHT]] ORDER BY m.SORT ORDER LIMIT 1';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchByAccountWithoutEventType()
    {
        $qb = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchByAccount('USER'));

        $expectedQuery = 'BLAH AND m.account = [[USER]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testApplyListFilters()
    {
        $this->setUpSut(Repo::class, true);

        $caseId = 1;
        $licenceId = 2;
        $organisationId = 3;
        $transportManagerId = 4;
        $userId = 5;
        $applicationId = 6;

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getLicence')
            ->andReturn($licenceId)
            ->twice()
            ->shouldReceive('getCase')
            ->andReturn($caseId)
            ->twice()
            ->shouldReceive('getOrganisation')
            ->andReturn($organisationId)
            ->twice()
            ->shouldReceive('getTransportManager')
            ->andReturn($transportManagerId)
            ->twice()
            ->shouldReceive('getUser')
            ->andReturn($userId)
            ->twice()
            ->shouldReceive('getApplication')
            ->andReturn($applicationId)
            ->twice();

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $qb->shouldReceive('expr->eq')->with('m.licence', ':licenceId')->once()->andReturn('licence');
        $qb->shouldReceive('orWhere')->with('licence')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('licenceId', $licenceId)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.case', ':caseId')->once()->andReturn('case');
        $qb->shouldReceive('orWhere')->with('case')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('caseId', $caseId)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.organisation', ':organisationId')->once()->andReturn('organisation');
        $qb->shouldReceive('orWhere')->with('organisation')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('organisationId', $organisationId)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')
            ->with('m.transportManager', ':transportManagerId')->once()->andReturn('transportManager');
        $qb->shouldReceive('orWhere')->with('transportManager')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('transportManagerId', $transportManagerId)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.user', ':userId')->once()->andReturn('user');
        $qb->shouldReceive('orWhere')->with('user')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('userId', $userId)->once()->andReturnSelf();

        $qb->shouldReceive('expr->eq')->with('m.application', ':applicationId')->once()->andReturn('application');
        $qb->shouldReceive('orWhere')->with('application')->once()->andReturnSelf();
        $qb->shouldReceive('setParameter')->with('applicationId', $applicationId)->once()->andReturnSelf();

        $this->queryBuilder->shouldReceive('modifyQuery')->with($qb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('eventHistoryType')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withUser')->once()->andReturnSelf();

        $this->sut->applyListFilters($qb, $query);
    }

    public function testFetchEventHistoryDetails()
    {

        $table = 'application_hist';
        $id = 1;
        $version = 2;
        $results = [
            [
                'foo' => 'bar2',
                'cake' => 'baz2',
                'same' => 'value',
                'version' => 2
            ],
            [
                'foo' => 'bar1',
                'cake' => 'baz1',
                'same' => 'value',
                'version' => 1
            ]
        ];

        $this->dbQueryService
            ->shouldReceive('get')
            ->with('EventHistory\GetEventHistoryDetails')
            ->andReturn(
                m::mock()
                ->shouldReceive('execute')
                ->with(
                    ['id' => $id, 'version' => [$version, $version - 1]]
                )
                ->andReturn(
                    m::mock()
                    ->shouldReceive('fetchAll')
                    ->andReturn($results)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->shouldReceive('setHistoryTable')
                ->with($table)
                ->once()
                ->getMock()
            );

        $expected = [
            [
                'name' => 'foo',
                'oldValue' => 'bar1',
                'newValue' => 'bar2'
            ],
            [
                'name' => 'cake',
                'oldValue' => 'baz1',
                'newValue' => 'baz2'
            ]
        ];

        $this->assertEquals($expected, $this->sut->fetchEventHistoryDetails($id, $version, $table));
    }

    public function testApplyListJoins()
    {
        $this->setUpSut(Repo::class, true);

        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->once()->with($qb)->andReturnSelf()
            ->shouldReceive('withRefData')->once()->andReturnSelf()
            ->shouldReceive('with')->with('case')->once()->andReturnSelf()
            ->shouldReceive('with')->with('licence')->once()->andReturnSelf()
            ->shouldReceive('with')->with('application')->once()->andReturnSelf()
            ->shouldReceive('with')->with('organisation')->once()->andReturnSelf()
            ->shouldReceive('with')->with('transportManager')->once()->andReturnSelf()
            ->shouldReceive('with')->with('busReg')->once()->andReturnSelf();

        $this->assertNull($this->sut->applyListJoins($qb));
    }
}
