<?php

namespace OlcsTest\Db\Service\Search;

use Elastica\Request;
use Olcs\Db\Exceptions\SearchDateFilterParseException;
use Olcs\Db\Service\Search\Search as SearchService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationService;
use \Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * @covers \Olcs\Db\Service\Search\Search
 */
class SearchTest extends MockeryTestCase
{
    /** @var  SearchService */
    private $sut;

    /** @var  m\MockInterface | AuthorizationService */
    private $mockAuthSrv;
    /** @var  m\MockInterface | \Elastica\Client */
    private $mockClient;
    /** @var  m\MockInterface | \Dvsa\Olcs\Api\Entity\User\User */
    private $mockUser;

    public function setUp()
    {
        $this->sut = new SearchService();

        $this->mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $this->mockUser->shouldReceive('getUser')->andReturnSelf();

        $this->mockAuthSrv = m::mock(AuthorizationService::class);
        $this->mockAuthSrv->shouldReceive('getIdentity->getUser')->andReturn($this->mockUser);
        $this->sut->setAuthService($this->mockAuthSrv);

        $this->mockClient = m::mock(\Elastica\Client::class);
        $this->sut->setClient($this->mockClient);

        $this->mockedSmServices = [
            AuthorizationService::class => $this->mockAuthSrv,
        ];

        parent::setUp();
    }

    /**
     * Tests the filter methods and functionality.
     *
     * @dataProvider filterFunctionalityDataProvider
     */
    public function testFilterFunctionality(array $setFilters, array $getFilters, array $filterNames)
    {
        // Uses fluent interface to test
        $this->assertEquals($getFilters, $this->sut->setFilters($setFilters)->getFilters());

        //die(var_export($systemUnderTest->getFilterNames(), 1));

        $this->assertEquals($filterNames, $this->sut->getFilterNames());
    }

    /**
     * Data provider for testFilterFunctionality test.
     *
     * @return array
     */
    public function filterFunctionalityDataProvider()
    {
        return [
            [
                'setFilters' => [
                    'organisationName' => 'a',
                    'licenceTrafficArea' => 'b',
                ],
                'getFilters' => [
                    'organisation_name' => 'a',
                    'licence_traffic_area' => 'b',
                ],
                'filterNames' => [
                    'organisation_name',
                    'licence_traffic_area',
                ],
            ],
        ];
    }

    public function testUpdateVehicleSection26()
    {
        $ids = [511, 2015];
        $section26Value = true;

        $expectedQuery = [
            'query' => [
                'bool' => [
                    'should' => [
                        ['match' => ['veh_id' => 511]],
                        ['match' => ['veh_id' => 2015]],
                    ]
                ]
            ],
            'size' => 1000,
        ];

        $searchResponse = m::mock(\Elastica\Response::class);
        $searchResponse->shouldReceive('getData')->andReturn(
            [
                'hits' => [
                    'hits' => [
                        ['id' => 'zz']
                    ]
                ]
            ]
        );

        $this->mockClient->shouldReceive('request')
            ->with('vehicle_current,vehicle_removed/_search', 'GET', $expectedQuery, [])
            ->once()
            ->andReturn($searchResponse);

        $bulkResponse = m::mock(\Elastica\Response::class);
        $bulkResponse->shouldReceive('getData');

        $this->mockClient->shouldReceive('request')
            ->with(
                '_bulk',
                'POST',
                '{"update":{"_id":{},"_type":{},"_index":{}}}' . "\n" . '{"doc":{"section_26":1}}' . "\n",
                [],
                Request::NDJSON_CONTENT_TYPE
            )
            ->once()
            ->andReturn($bulkResponse);

        $this->sut->updateVehicleSection26($ids, $section26Value);
    }

    public function testUpdateVehicleSection26NoResults()
    {
        $ids = [511, 2015];
        $section26Value = true;

        $expectedQuery = [
            'query' => [
                'bool' => [
                    'should' => [
                        ['match' => ['veh_id' => 511]],
                        ['match' => ['veh_id' => 2015]],
                    ]
                ]
            ],
            'size' => 1000,
        ];

        $searchResponse = m::mock(\Elastica\Response::class);
        $searchResponse->shouldReceive('getData')->andReturn([]);

        $this->mockClient->shouldReceive('request')
            ->with('vehicle_current,vehicle_removed/_search', 'GET', $expectedQuery, [])->once()
            ->andReturn($searchResponse);

        $this->sut->setClient($this->mockClient);

        $this->sut->updateVehicleSection26($ids, $section26Value);
    }

    public function testSearchIndexInternal()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);

        $this->mockClient->shouldReceive('request')->once()->andReturnUsing(
            function ($path, $method, $query, $params) {
                $this->assertSame('licence/_search', $path);
                $this->assertSame('GET', $method);

                $this->assertArrayHasKey('query', $query);
                $this->assertSame(['foo' => 'desc'], $query['sort']);
                $this->assertSame(0, $query['from']);
                $this->assertSame(10, $query['size']);
                $this->assertSame([], $params);

                $searchResponse = m::mock(\Elastica\Response::class);
                $searchResponse->shouldReceive('getData')->andReturn([]);
                return $searchResponse;
            }
        );

        $this->sut->setClient($this->mockClient);
        $this->sut->setSort('foo');
        $this->sut->setOrder('desc');

        $this->sut->search('FOO', ['licence']);
    }

    public function testSearchIndexExternal()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);

        $this->mockClient->shouldReceive('request')->once()->andReturnUsing(
            function ($path, $method, $query, $params) {
                $this->assertSame('licence/_search', $path);
                $this->assertSame('GET', $method);

                $this->assertArrayHasKey('query', $query);
                $this->assertSame(['foo' => 'desc'], $query['sort']);
                $this->assertSame(0, $query['from']);
                $this->assertSame(10, $query['size']);
                $this->assertSame([], $params);

                $searchResponse = m::mock(\Elastica\Response::class);
                $searchResponse->shouldReceive('getData')->andReturn([]);
                return $searchResponse;
            }
        );

        $this->sut->setSort('foo');
        $this->sut->setOrder('desc');
        $this->sut->setFilters(
            [
                'organisationName' => 'a',
                'licenceTrafficArea' => 'b',
            ]
        );

        $this->sut->search('FOO', ['licence']);
    }

    public function testSearchIndexAnon()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(true);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);

        $this->mockClient->shouldReceive('request')->once()->andReturnUsing(
            function ($path, $method, $query, $params) {
                $this->assertSame('licence/_search', $path);
                $this->assertSame('GET', $method);

                $this->assertArrayHasKey('query', $query);
                $this->assertSame(['foo' => 'desc'], $query['sort']);
                $this->assertSame(0, $query['from']);
                $this->assertSame(10, $query['size']);
                $this->assertSame([], $params);

                $searchResponse = m::mock(\Elastica\Response::class);
                $searchResponse->shouldReceive('getData')->andReturn([]);
                return $searchResponse;
            }
        );

        $this->sut->setSort('foo');
        $this->sut->setOrder('desc');

        $this->sut->search('FOO', ['licence']);
    }

    public function testSearchIndexQueryTemplateNotFound()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);

        $this->setExpectedException(
            \RuntimeException::class,
            'Cannot generate an elasticsearch query, is the template missing'
        );

        $this->sut->setSort('foo');
        $this->sut->setOrder('desc');

        $this->sut->search('FOO', ['MISSING']);
    }

    /**
     * @dataProvider setDateRangesDataProvider
     */
    public function testSetDateRanges($data, $expect)
    {
        $this->sut->setDateRanges($data);

        $this->assertSame($expect, $this->sut->getDateRanges());
    }

    public function setDateRangesDataProvider()
    {
        return [
            // valid from string
            [
                [
                    'field1' => '2010-02-01',
                ],
                [
                    'field1' => '2010-02-01',
                ]
            ],
            // valid from array
            [
                [
                    'field1' => [
                        'year' => '2010',
                        'month' => '02',
                        'day' => '01',
                    ],
                    'field2' => [
                        'year' => '2010',
                        'month' => '2',
                        'day' => '1',
                    ],
                ],
                [
                    'field1' => '2010-02-01',
                    'field2' => '2010-02-01',
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidDateProvider
     * @expectedException \Olcs\Db\Exceptions\SearchDateFilterParseException
     */
    public function testInvalidDateFilter($data)
    {
        $this->sut->setDateRanges($data);
    }

    public function invalidDateProvider()
    {
        return    // invalid
            [
                [
                    [
                        'field6' => [
                            'year' => '2010',
                            'month' => '02',
                            'day' => '',
                        ],
                        'field7' => [
                            'year' => '',
                            'month' => '02',
                            'day' => '01',
                        ],
                        'field8' => [
                            'year' => '2010',
                            'month' => '13',
                            'day' => '01',
                        ],
                        'field9' => [
                            'year' => '2017',
                            'month' => '02',
                            'day' => '',
                        ],
                        'field10' => [
                            'year' => '2010',
                            'month' => '02',
                            'day' => '53',
                        ],

                    ],
                ]
            ];
    }

    public function testSearchUnderMaxResults()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);

        $searchResponse = m::mock(\Elastica\Response::class);
        $searchResponse->shouldReceive('getData')->andReturn(
            ['hits' => ['total' => SearchService::MAX_NUMBER_OF_RESULTS - 1]]
        );
        $this->mockClient->shouldReceive('request')->once()->andReturn($searchResponse);

        $result = $this->sut->search('FOO', ['licence']);
        $this->assertSame(SearchService::MAX_NUMBER_OF_RESULTS - 1, $result['Count']);
    }

    public function testSearchOverMaxResults()
    {
        $this->mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $this->mockAuthSrv
            ->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false)
            ->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);

        $searchResponse = m::mock(\Elastica\Response::class);
        $searchResponse->shouldReceive('getData')->andReturn(
            ['hits' => ['total' => SearchService::MAX_NUMBER_OF_RESULTS + 1]]
        );
        $this->mockClient->shouldReceive('request')->once()->andReturn($searchResponse);

        $result = $this->sut->search('FOO', ['licence']);
        $this->assertSame(SearchService::MAX_NUMBER_OF_RESULTS, $result['Count']);
    }
}
