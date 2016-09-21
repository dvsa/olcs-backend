<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\Search as SearchService;
use Mockery as m;
use PHPUnit_Framework_TestCase as TestCase;
use Elastica\Query as Query;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationService;

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

    public function testProcessDateRanges()
    {
        $bool = new Query\Bool();

        $service = $this->getMock(SearchService::class, null);
        $service->setDateRanges(
            [
                'dateOneFrom' => ['year' => '2015', 'month' => '01', 'day' => '02'],
                'dateOneTo'   => '2015-03-01',
                'dateTwoFrom' => '2015-02-01',
                'dateTwoTo'   => '2015-04-01'
            ]
        );

        $result = array (
            'bool' => array (
                'must' => array (
                    0 => array (
                        'range' => array (
                            'date_one' => array (
                                'from' => '2015-01-02',
                                'to' => '2015-03-01',
                            ),
                        ),
                    ),
                    1 => array (
                        'range' => array (
                            'date_two' => array (
                                'from' => '2015-02-01',
                                'to' => '2015-04-01',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSame($result, $service->processDateRanges($bool)->toArray());
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

    /**
     * @dataProvider searchIndexProvider
     *
     * @param $index
     * @param $expectedQuery
     * @param string $userType
     */
    public function testSearchIndex($index, $expectedQuery, $userType = 'external', $searchString = 'FOO BAR')
    {
        $sut = new SearchService();

        $sut->setSort('foo');
        $sut->setOrder('desc');

        $authService = m::mock(AuthorizationService::class);

        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')
            ->andReturnSelf();

        if ($userType === 'internal') {
            $mockUser->shouldReceive('isAnonymous')
                ->zeroOrMoreTimes()
                ->andReturn(false);
            $authService->shouldReceive('isGranted')
                ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
                ->zeroOrMoreTimes()
                ->andReturn(true);
            $authService->shouldReceive('isGranted')
                ->zeroOrMoreTimes()
                ->with(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER, null)
                ->andReturn(false);
        } else {
            $authService->shouldReceive('isGranted')->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
                ->andReturn(false);
        }

        $authService->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $sut->setAuthService($authService);

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')->once()->andReturnUsing(
            function ($path, $method, $query, $params) use ($index, $expectedQuery) {
                $this->assertSame($index . '/_search', $path);
                $this->assertSame('GET', $method);
                $this->assertSame($expectedQuery, $query);
                $this->assertSame([], $params);

                $searchResponse = m::mock(\Elastica\Response::class);
                $searchResponse->shouldReceive('getData')->andReturn([]);
                return $searchResponse;
            }
        );

        $sut->setClient($mockClient);

        $sut->search($searchString, [$index]);
    }

    public function searchIndexProvider()
    {
        return [
            $this->getExpectedAddressSearch(),
            $this->getExpectedApplicationSearch(),
            $this->getExpectedCaseSearch(),
            $this->getExpectedOperatorSearch(),
            $this->getExpectedIrfoSearch(),
            $this->getExpectedLicenceSearch(),
            $this->getExpectedPsvDiscSearch(),
            $this->getExpectedPublicationSearch(),
            $this->getExpectedUserSearch(),
            $this->getExpectedVehicleCurrentSearch(),
            $this->getExpectedVehicleRemovedSearch(),
            $this->getExpectedPersonSearchForInternalUser(),
            $this->getExpectedPersonSearchForInternalUserIdLookup(),
            $this->getExpectedPersonSearchForExternalUser(),
            $this->getExpectedBusRegSearch()
        ];
    }

    private function getExpectedAddressSearch()
    {
        return [
            'address',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('postcode', 'FOO BAR'),
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedApplicationSearch()
    {
        return [
            'application',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('correspondence_postcode', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', 2.0)
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedCaseSearch()
    {
        return [
            'case',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('correspondence_postcode', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedOperatorSearch()
    {
        return [
            'operator',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('postcode', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedIrfoSearch()
    {
        return [
            'irfo',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedLicenceSearch()
    {
        return [
            'licence',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedPsvDiscSearch()
    {
        return [
            'psv_disc',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedPublicationSearch()
    {
        return [
            'publication',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ],
                                    'must' => [
                                        0 => $this->generateMatch('pub_status', 'pub_s_printed')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedUserSearch()
    {
        return [
            'user',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0'),
                                        $this->generateMatch(
                                            'login_id',
                                            ['query' => 'FOO BAR', 'boost' => 2.0]
                                        ),
                                        $this->generateMatch(
                                            'lic_nos',
                                            ['query' => 'FOO BAR']
                                        )
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'internal',
            'FOO BAR'
        ];
    }

    private function getExpectedVehicleCurrentSearch()
    {
        return [
            'vehicle_current',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('vrm', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedVehicleRemovedSearch()
    {
        return [
            'vehicle_removed',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch('vrm', 'FOO BAR'),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedPersonSearchForExternalUser()
    {
        return [
            'person',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('person_family_name_wildcard', '*foo bar*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*foo bar*', '1.0'),
                                        $this->generateWildcard('person_family_name_wildcard', '*foo*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*foo*', '1.0'),
                                        $this->generateWildcard('person_family_name_wildcard', '*bar*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*bar*', '1.0'),
                                    ],
                                    'must_not' => [
                                        $this->generateMatch('tm_status_id', 'tm_s_rem'),
                                    ],
                                    'must' => [
                                        0 => [
                                            'filtered' => [
                                                'filter' => [
                                                    'exists' => [
                                                        'field' => 'lic_id'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function getExpectedPersonSearchForInternalUser()
    {
        return [
            'person',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateWildcard('person_family_name_wildcard', '*foo bar*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*foo bar*', '1.0'),
                                        $this->generateWildcard('person_family_name_wildcard', '*foo*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*foo*', '1.0'),
                                        $this->generateWildcard('person_family_name_wildcard', '*bar*', '2.0'),
                                        $this->generateWildcard('person_forename_wildcard', '*bar*', '1.0'),
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'internal',
            'FOO BAR'
        ];
    }

    private function getExpectedPersonSearchForInternalUserIdLookup()
    {
        return [
            'person',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', '123'),
                                        $this->generateBoostedMatch('person_id', '123', 2.0),
                                        $this->generateBoostedMatch('tm_id', '123', 2.0),
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'internal',
            '123'
        ];
    }

    private function getExpectedBusRegSearch()
    {
        return [
            'busreg',
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            0 => [
                                'bool' => [
                                    'should' => [
                                        $this->generateMatch('_all', 'FOO BAR'),
                                        $this->generateMatch(
                                            'reg_no',
                                            [
                                                'query' => 'FOO BAR',
                                                'boost' => 2.0,
                                            ]
                                        ),
                                        $this->generateWildcard('org_name_wildcard', 'foo bar*', '2.0')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'foo' => 'desc'
                ],
                'size' => 10,
                'from' => 0
            ],
            'external',
            'FOO BAR'
        ];
    }

    private function generateBoostedMatch($field, $value, $boost)
    {
        return $this->generateMatch(
            $field,
            [
                'query' => $value,
                'boost' => (float) $boost,
            ]
        );
    }

    private function generateMatch($field, $value)
    {
        return [
            'match' => [
                $field => $value,
            ]
        ];
    }

    private function generateWildcard($name, $value, $boost)
    {
        return [
            'wildcard' => [
                $name => [
                    'value' => $value,
                    'boost' => (float) $boost,
                ]
            ]
        ];
    }

    public function testInternalQueryTemplate()
    {
        $sut = new SearchService();

        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $mockUser->shouldReceive('getUser')->andReturnSelf();
        $mockUser->shouldReceive('isAnonymous')->zeroOrMoreTimes()->andReturn(false);

        $authService = m::mock(AuthorizationService::class);
        $authService->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
            ->zeroOrMoreTimes()
            ->andReturn(true);
        $authService->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER, null)
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $authService->shouldReceive('getIdentity->getUser')->andReturn($mockUser);

        $sut->setAuthService($authService);

        $mockClient = m::mock(\Elastica\Client::class);
        $mockClient->shouldReceive('request')->once()->andReturnUsing(
            function ($path, $method, $query, $params) {
                $this->assertSame('/_search', $path);
                $this->assertSame('GET', $method);
                $this->assertArrayHasKey('query', $query);
                $this->assertSame([], $params);

                $searchResponse = m::mock(\Elastica\Response::class);
                $searchResponse->shouldReceive('getData')->andReturn([]);
                return $searchResponse;
            }
        );

        $sut->setClient($mockClient);

        $sut->search('foo', ['licence']);

    }
}
