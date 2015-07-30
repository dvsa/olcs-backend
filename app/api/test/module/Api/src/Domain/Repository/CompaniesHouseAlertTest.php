<?php

/**
 * CompaniesHouseAlert test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseAlert as CompaniesHouseAlertRepo;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as CompanyEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\AlertList as AlertListQry;
use Mockery as m;

/**
 * CompaniesHouseAlert test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlertTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(CompaniesHouseAlertRepo::class, true);
    }

    public function testFetchListDefault()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'includeClosed' => '',
            'typeOfChange' => '',
        ];

        $query = AlertListQry::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY} AND ca.isClosed = 0';

        $this->assertEquals($expected, $this->query);
    }

    public function testFetchListIncludeClosedAndFilterType()
    {
        $mockQb = $this->createMockQb('{QUERY}');
        $this->mockCreateQueryBuilder($mockQb);

        $data = [
            'includeClosed' => 1,
            'typeOfChange' => 'some_type',
        ];

        $query = AlertListQry::create($data);

        $this->sut->shouldReceive('fetchPaginatedList')
            ->once()
            ->with($mockQb, Query::HYDRATE_ARRAY)
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('buildDefaultListQuery')
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchList($query));

        $expected = '{QUERY} INNER JOIN ca.reasons r WITH r.reasonType = [[some_type]]';

        $this->assertEquals($expected, $this->query);
    }

    public function testGetReasonValueOptions()
    {
        /** @var QueryBuilder $qb */
        $qb = m::mock(QueryBuilder::class);

        $mockRefDataRepo = m::mock();

        $this->em
            ->shouldReceive('getRepository')
            ->with(RefData::class)
            ->andReturn($mockRefDataRepo);

        $mockRefDataRepo
            ->shouldReceive('createQueryBuilder')
            ->with('r')
            ->andReturn($qb);

        $where = m::mock();
        $qb
            ->shouldReceive('where')
            ->once()
            ->with($where)
            ->andReturnSelf();
        $qb
            ->shouldReceive('expr->eq')
            ->once()
            ->with('r.refDataCategoryId', ':categoryId')
            ->andReturn($where);
        $qb
            ->shouldReceive('setParameter')
            ->once()
            ->with('categoryId', 'ch_alert_reason')
            ->andReturnSelf();

        $result = [
            [
                'id' => 'reason_1',
                'description' => 'Reason 1',
            ],
            [
                'id' => 'reason_2',
                'description' => 'Reason 2',
            ],
        ];

        $qb
            ->shouldReceive('getQuery->getArrayResult')
            ->once()
            ->andReturn($result);

        $this->assertEquals(
            [
                'reason_1' => 'Reason 1',
                'reason_2' => 'Reason 2',
            ],
            $this->sut->getReasonValueOptions()
        );
    }
}
