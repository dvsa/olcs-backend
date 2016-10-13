<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\DocumentSearchView as DocumentSearchViewRepo;
use Dvsa\Olcs\Transfer\Query\Document\DocumentList;
use Dvsa\Olcs\Utils\Constants\FilterOptions;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\Repository\DocumentSearchView
 */
class DocumentSearchViewTest extends RepositoryTestCase
{

    public function setUp()
    {
        $this->setUpSut(DocumentSearchViewRepo::class, true);
    }

    public function testFetchListWithoutFilters()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [];

        $query = DocumentList::create($data);

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

    public function testFetchList()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'isExternal' => 'Y',
            'category' => 11,
            'documentSubCategory' => 22,
            'licence' => 111,
            'transportManager' => 222,
            'case' => 333,
            'irfoOrganisation' => 444,
            'showDocs' => 'OTHER',
        ];

        $query = DocumentList::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY} AND m.isExternal = [[1]]' .
            ' AND m.category = 11' .
            ' AND m.documentSubCategory IN 22' .
            ' AND (m.licenceId = :licence OR m.tmId = :tm OR m.caseId = :case' .
            ' OR m.irfoOrganisationId = :irfoOrganisation)';

        $this->assertEquals($expected, $this->query);
    }

    public function testFetchListWithShowSelfOnly()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'case' => 'unit_CaseId',
            'irfoOrganisation' => 444,
            'showDocs' => FilterOptions::SHOW_SELF_ONLY,
            'application' => 'unit_AppId',
            'busReg' => 'unit_BusReg',
        ];

        $query = DocumentList::create($data);

        $this->sut
            ->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            //
            ->shouldReceive('buildDefaultListQuery')->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY}' .
            ' AND m.applicationId = [[unit_AppId]]' .
            ' AND m.caseId = [[unit_CaseId]]' .
            ' AND m.busRegId = [[unit_BusReg]]' .
            ' AND ('.
                'm.irfoOrganisationId = :irfoOrganisation' .
            ')';

        $this->assertEquals($expected, $this->query);
    }
}
