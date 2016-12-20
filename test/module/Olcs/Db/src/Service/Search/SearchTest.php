<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\Search as SearchService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationService;
use \Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Class Search Test
 *
 * @package OlcsTest\Db\Service\Search
 */
class SearchTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    /**
     * Tests the filter methods and functionality.
     *
     * @dataProvider filterFunctionalityDataProvider
     *
     * @param array $setFilters
     * @param array $getFilters
     * @param array $filterNames
     *
     * @return void
     */
    public function testFilterFunctionality(array $setFilters, array $getFilters, array $filterNames)
    {
        $systemUnderTest = new SearchService();

        // Uses fluent interface to test
        $this->assertEquals($getFilters, $systemUnderTest->setFilters($setFilters)->getFilters());

        //die(var_export($systemUnderTest->getFilterNames(), 1));

        $this->assertEquals($filterNames, $systemUnderTest->getFilterNames());
    }

    /**
     * Data provider for testFilterFunctionality test.
     *
     * @return array
     */
    public function filterFunctionalityDataProvider()
    {
        return array(
            array(
                array( // set filters
                    'organisationName' => 'a',
                    'licenceTrafficArea' => 'b'
                ),
                array( // get filters
                    'organisation_name' => 'a',
                    'licence_traffic_area' => 'b'
                ),
                array( // names
                    'organisation_name',
                    'licence_traffic_area'
                )
            ),
        );
    }

    public function testUpdateVehicleSection26()
    {
        $ids = [511, 2015];
        $section26Value = true;
        $sut = new SearchService();

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

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')
            ->with('vehicle_current,vehicle_removed/_search', 'GET', $expectedQuery, [])->once()
            ->andReturn($searchResponse);

        $bulkResponse = m::mock(\Elastica\Response::class);
        $bulkResponse->shouldReceive('getData');

        $mockClient->shouldReceive('request')
            ->with(
                '_bulk',
                'PUT',
                '{"update":{"_id":{},"_type":{},"_index":{}}}'."\n".'{"doc":{"section_26":1}}'."\n",
                []
            )->once()->andReturn($bulkResponse);

        $sut->setClient($mockClient);

        $sut->updateVehicleSection26($ids, $section26Value);
    }

    public function testUpdateVehicleSection26NoResults()
    {
        $ids = [511, 2015];
        $section26Value = true;
        $sut = new SearchService();

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

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')
            ->with('vehicle_current,vehicle_removed/_search', 'GET', $expectedQuery, [])->once()
            ->andReturn($searchResponse);

        $sut->setClient($mockClient);

        $sut->updateVehicleSection26($ids, $section26Value);
    }

    public function testSearchIndexInternal()
    {
        $sut = new SearchService();

        $sut->setSort('foo');
        $sut->setOrder('desc');

        $authService = m::mock(AuthorizationService::class);
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')->andReturnSelf();
        $mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);
        $authService->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true);
        $authService->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);
        $authService->shouldReceive('getIdentity->getUser')->andReturn($mockUser);
        $sut->setAuthService($authService);

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')->once()->andReturnUsing(
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

        $sut->setClient($mockClient);

        $sut->search('FOO', ['licence']);
    }

    public function testSearchIndexExternal()
    {
        $sut = new SearchService();

        $sut->setSort('foo');
        $sut->setOrder('desc');

        $authService = m::mock(AuthorizationService::class);
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')->andReturnSelf();
        $mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);
        $authService->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
        $authService->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
        $authService->shouldReceive('getIdentity->getUser')->andReturn($mockUser);
        $sut->setAuthService($authService);

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')->once()->andReturnUsing(
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

        $sut->setClient($mockClient);

        $sut->search('FOO', ['licence']);
    }

    public function testSearchIndexAnon()
    {
        $sut = new SearchService();

        $sut->setSort('foo');
        $sut->setOrder('desc');

        $authService = m::mock(AuthorizationService::class);
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')->andReturnSelf();
        $mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(true);
        $authService->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(false);
        $authService->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(false);
        $authService->shouldReceive('getIdentity->getUser')->andReturn($mockUser);
        $sut->setAuthService($authService);

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')->once()->andReturnUsing(
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

        $sut->setClient($mockClient);

        $sut->search('FOO', ['licence']);
    }

    public function testSearchIndexQueryTemplateNotFound()
    {
        $sut = new SearchService();

        $sut->setSort('foo');
        $sut->setOrder('desc');

        $authService = m::mock(AuthorizationService::class);
        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')->andReturnSelf();
        $mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);
        $authService->shouldReceive('isGranted')->with(Permission::INTERNAL_USER, null)->andReturn(true);
        $authService->shouldReceive('isGranted')->with(Permission::SELFSERVE_USER, null)->andReturn(true);
        $authService->shouldReceive('getIdentity->getUser')->andReturn($mockUser);
        $sut->setAuthService($authService);

        $this->setExpectedException(
            \RuntimeException::class,
            'Cannot generate an elasticsearch query, is the template missing'
        );
        $sut->search('FOO', ['MISSING']);
    }

    /**
     * @dataProvider setDateRangesDataProvider
     */
    public function testSetDateRanges($data, $expect)
    {
        $sut = new SearchService();

        $sut->setDateRanges($data);

        $this->assertSame($expect, $sut->getDateRanges());
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
            // invalid
            [
                [
                    'field1' => '2010',
                    'field2' => '2010-02',
                    'field3' => '01-02-2010',
                    'field4' => '02-2010',
                    'field5' => '',
                    'field6' => [
                        'year' => '2010',
                        'month' => '02',
                        'day' => '',
                    ],
                    'field7' => [
                        'year' => '2010',
                        'month' => '',
                        'day' => '',
                    ],
                    'field8' => [
                        'year' => '',
                        'month' => '2',
                        'day' => '',
                    ],
                    'field9' => [
                        'year' => '',
                        'month' => '',
                        'day' => '1',
                    ],
                    'field10' => [
                        'day' => '',
                        'month' => '',
                        'year' => ''
                    ],
                ],
                [
                ]
            ],
        ];
    }
}
