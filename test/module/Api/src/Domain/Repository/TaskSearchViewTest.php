<?php

/**
 * Task Search View Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Query\Task\TaskList;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TaskSearchView as TaskSearchViewRepo;

/**
 * Task Search View Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskSearchViewTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TaskSearchViewRepo::class, true);
    }

    public function testFetchList()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'assignedToUser' => 11,
            'assignedToTeam' => 22,
            'category' => 1,
            'taskSubCategory' => 2,
            'date' => 'tdt_today',
            'status' => 'tst_closed',
            'urgent' => true,
            'licence' => 111,
            'transportManager' => 222,
            'case' => 333,
            'application' => 444,
            'busReg' => 555,
            'organisation' => 666,
        ];

        $query = TaskList::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY} AND m.assignedToUser = 11'
            . ' AND m.assignedToTeam = 22'
            . ' AND m.category = 1'
            . ' AND m.taskSubCategory = 2'
            . ' AND m.actionDate <= [[' . date('Y-m-d') . ']]'
            . ' AND m.isClosed = 1'
            . ' AND m.urgent = 1'
            . ' AND ('
                . 'm.licenceId = :licence'
                . ' OR m.transportManagerId = :tm'
                . ' OR m.caseId = :case'
                . ' OR m.applicationId = :application'
                . ' OR m.busRegId = :busReg'
                . ' OR m.irfoOrganisationId = :organisation'
            . ')';

        $this->assertEquals($expected, $this->query);
    }

    public function testFetchListAlt()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'assignedToUser' => 11,
            'assignedToTeam' => 22,
            'category' => 1,
            'taskSubCategory' => 2,
            'date' => 'tdt_today',
            'status' => 'tst_all',
            'urgent' => false,
            'licence' => 111,
            'application' => 444,
        ];

        $query = TaskList::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY} AND m.assignedToUser = 11'
            . ' AND m.assignedToTeam = 22'
            . ' AND m.category = 1'
            . ' AND m.taskSubCategory = 2'
            . ' AND m.actionDate <= [[' . date('Y-m-d') . ']]'
            . ' AND ('
            . 'm.licenceId = :licence'
            . ' OR m.applicationId = :application'
            . ')';

        $this->assertEquals($expected, $this->query);
    }

    public function testFetchNoFilters()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [];

        $query = TaskList::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY}';

        $this->assertEquals($expected, $this->query);
    }
}
