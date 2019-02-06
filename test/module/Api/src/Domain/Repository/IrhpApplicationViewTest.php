<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Transfer\Query\IrhpApplication\GetAllByLicence;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\GetAllByOrganisation;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplicationView;

/**
 * IrhpApplicationView test
 */
class IrhpApplicationViewTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(IrhpApplicationView::class);
    }

    public function testFetchListByLicence()
    {
        $this->setUpSut(IrhpApplicationView::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('paginate')->andReturnSelf();

        $query = GetAllByLicence::create(['licence' => 'ID']);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND iav.licenceId = ID';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListByLicenceWithStatus()
    {
        $this->setUpSut(IrhpApplicationView::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('paginate')->andReturnSelf();

        $query = GetAllByLicence::create(
            [
                'licence' => 'ID',
                'irhpApplicationStatuses' => ['S1', 'S2']
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND iav.licenceId = ID '
            . 'AND iav.statusId IN ["S1","S2"]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListByOrganisation()
    {
        $this->setUpSut(IrhpApplicationView::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('paginate')->andReturnSelf();

        $query = GetAllByOrganisation::create(['organisation' => 'ID']);
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND iav.organisationId = ID';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchListByOrganisationWithStatus()
    {
        $this->setUpSut(IrhpApplicationView::class, true);
        $this->sut->shouldReceive('fetchPaginatedList')->andReturn(['RESULTS']);

        $qb = $this->createMockQb('BLAH');
        $this->mockCreateQueryBuilder($qb);

        $this->queryBuilder
            ->shouldReceive('modifyQuery')->with($qb)->andReturnSelf()
            ->shouldReceive('withRefdata')->once()->andReturnSelf()
            ->shouldReceive('paginate')->andReturnSelf();

        $query = GetAllByOrganisation::create(
            [
                'organisation' => 'ID',
                'irhpApplicationStatuses' => ['S1', 'S2']
            ]
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchList($query));

        $expectedQuery = 'BLAH '
            . 'AND iav.organisationId = ID '
            . 'AND iav.statusId IN ["S1","S2"]';
        $this->assertEquals($expectedQuery, $this->query);
    }
}
