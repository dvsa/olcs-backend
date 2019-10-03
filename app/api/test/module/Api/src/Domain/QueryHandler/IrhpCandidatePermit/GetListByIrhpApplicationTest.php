<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit\GetListByIrhpApplication as GetListByIrhpApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetListByIrhpApplication as GetListByIrhpApplicationQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class GetListByIrhpApplicationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(GetListByIrhpApplicationHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('Country', CountryRepo::class);

        $this->mockedSmServices['config'] = [
            'permits' => [
                'types' => [
                    IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT => [
                        'restricted_countries' => ['AT', 'GR', 'HU', 'IT', 'RU'],
                    ],
                    IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => [
                        'restricted_countries' => ['GR', 'HU', 'IT', 'RU'],
                    ],
                ]
            ],
        ];

        parent::setUp();
    }

    public function testHandleQueryWithEmptyList()
    {
        $irhpApplicationId = 10;
        $page = 1;
        $limit = 25;

        $query = GetListByIrhpApplicationQry::create(
            [
                'irhpApplication' => $irhpApplicationId,
                'page' => $page,
                'limit' => $limit,
                'order' => 'id',
                'sort' => 'ASC',
            ]
        );

        $irhpCandidatePermits = [];
        $irhpCandidatePermitsCount = 0;

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($irhpCandidatePermits);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchCount')
            ->andReturn($irhpCandidatePermitsCount);

        $expectedBundle = [
            'irhpPermitRange' => [
                'countrys' => [
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $bundledIrhpCandidatePermits = [];

        $this->sut->shouldReceive('resultList')
            ->with($irhpCandidatePermits, $expectedBundle)
            ->andReturn($bundledIrhpCandidatePermits);

        $expectedResult = [
            'results' => [],
            'count' => 0,
        ];


        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider dpTestHandleQuery
     */
    public function testHandleQuery(
        $queryData,
        $bundledIrhpCandidatePermits,
        $irhpPermitTypeId,
        $expectedRestrictedCountriesFromConfig,
        $restrictedCountries,
        $expectedResult
    ) {
        $query = GetListByIrhpApplicationQry::create($queryData);

        $irhpCandidatePermits = [
            m::mock(IrhpCandidatePermit::class),
            m::mock(IrhpCandidatePermit::class),
            m::mock(IrhpCandidatePermit::class)
        ];
        $irhpCandidatePermitsCount = 3;

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($irhpCandidatePermits);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchCount')
            ->andReturn($irhpCandidatePermitsCount);

        $expectedBundle = [
            'irhpPermitRange' => [
                'countrys' => [
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $this->sut->shouldReceive('resultList')
            ->with($irhpCandidatePermits, $expectedBundle)
            ->andReturn($bundledIrhpCandidatePermits);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($irhpPermitTypeId);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType')
            ->withNoArgs()
            ->once()
            ->andReturn($irhpPermitType);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($queryData['irhpApplication'])
            ->andReturn($irhpApplication);

        $this->repoMap['Country']->shouldReceive('fetchByIds')
            ->with($expectedRestrictedCountriesFromConfig, Query::HYDRATE_ARRAY)
            ->andReturn($restrictedCountries);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    public function dpTestHandleQuery()
    {
        return [
            'ECMT Annual' => [
                'queryData' => [
                    'irhpApplication' => 10,
                    'page' => 1,
                    'limit' => 25,
                    'order' => 'id',
                    'sort' => 'ASC',
                ],
                'bundledIrhpCandidatePermits' => [
                    [
                        'id' => 463,
                        'irhpPermitRange' => [
                            'id' => 101,
                            'countrys' => [
                            ]
                        ]
                    ],
                    [
                        'id' => 464,
                        'irhpPermitRange' => [
                            'id' => 102,
                            'countrys' => [
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                    [
                        'id' => 465,
                        'irhpPermitRange' => [
                            'id' => 103,
                            'countrys' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                ],
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                'expectedRestrictedCountriesFromConfig' => ['AT', 'GR', 'HU', 'IT', 'RU'],
                'restrictedCountries' => [
                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ],
                'expectedResult' => [
                    'results' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                                'countrys' => [
                                ]
                            ],
                            'permitNumber' => 1,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                                'countrys' => [
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 2,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece']
                            ]
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                                'countrys' => [
                                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 3,
                            'constrainedCountries' => [
                            ]
                        ]
                    ],
                    'count' => 3
                ],
            ],
            'ECMT Annual with pagination' => [
                'queryData' => [
                    'irhpApplication' => 10,
                    'page' => 3,
                    'limit' => 25,
                    'order' => 'id',
                    'sort' => 'ASC',
                ],
                'bundledIrhpCandidatePermits' => [
                    [
                        'id' => 463,
                        'irhpPermitRange' => [
                            'id' => 101,
                            'countrys' => [
                            ]
                        ]
                    ],
                    [
                        'id' => 464,
                        'irhpPermitRange' => [
                            'id' => 102,
                            'countrys' => [
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                    [
                        'id' => 465,
                        'irhpPermitRange' => [
                            'id' => 103,
                            'countrys' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                ],
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                'expectedRestrictedCountriesFromConfig' => ['AT', 'GR', 'HU', 'IT', 'RU'],
                'restrictedCountries' => [
                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ],
                'expectedResult' => [
                    'results' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                                'countrys' => [
                                ]
                            ],
                            'permitNumber' => 51,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                                'countrys' => [
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 52,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece']
                            ]
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                                'countrys' => [
                                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 53,
                            'constrainedCountries' => [
                            ]
                        ]
                    ],
                    'count' => 3
                ],
            ],
            'ECMT Annual with bundle without irhpPermitRange' => [
                'queryData' => [
                    'irhpApplication' => 10,
                    'page' => 1,
                    'limit' => 25,
                    'order' => 'id',
                    'sort' => 'ASC',
                ],
                'bundledIrhpCandidatePermits' => [
                    [
                        'id' => 463,
                    ],
                    [
                        'id' => 464,
                    ],
                    [
                        'id' => 465,
                    ],
                ],
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                'expectedRestrictedCountriesFromConfig' => ['AT', 'GR', 'HU', 'IT', 'RU'],
                'restrictedCountries' => [
                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ],
                'expectedResult' => [
                    'results' => [
                        [
                            'id' => 463,
                            'permitNumber' => 1,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ],
                        [
                            'id' => 464,
                            'permitNumber' => 2,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ],
                        [
                            'id' => 465,
                            'permitNumber' => 3,
                            'constrainedCountries' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                    'count' => 3
                ],
            ],
            'ECMT Short-term' => [
                'queryData' => [
                    'irhpApplication' => 10,
                    'page' => 1,
                    'limit' => 25,
                    'order' => 'id',
                    'sort' => 'ASC',
                ],
                'bundledIrhpCandidatePermits' => [
                    [
                        'id' => 463,
                        'irhpPermitRange' => [
                            'id' => 101,
                            'countrys' => [
                            ]
                        ]
                    ],
                    [
                        'id' => 464,
                        'irhpPermitRange' => [
                            'id' => 102,
                            'countrys' => [
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                    [
                        'id' => 465,
                        'irhpPermitRange' => [
                            'id' => 103,
                            'countrys' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia']
                            ]
                        ]
                    ],
                ],
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'expectedRestrictedCountriesFromConfig' => ['GR', 'HU', 'IT', 'RU'],
                'restrictedCountries' => [
                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ],
                'expectedResult' => [
                    'results' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                                'countrys' => [
                                ]
                            ],
                            'permitNumber' => 1,
                            'constrainedCountries' => [
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia'],
                            ],
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                                'countrys' => [
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                                ]
                            ],
                            'permitNumber' => 2,
                            'constrainedCountries' => [
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                            ],
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                                'countrys' => [
                                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                                ]
                            ],
                            'permitNumber' => 3,
                            'constrainedCountries' => [],
                        ]
                    ],
                    'count' => 3
                ],
            ],
            'ECMT Removal' => [
                'queryData' => [
                    'irhpApplication' => 10,
                    'page' => 1,
                    'limit' => 25,
                    'order' => 'id',
                    'sort' => 'ASC',
                ],
                'bundledIrhpCandidatePermits' => [
                    [
                        'id' => 463,
                        'irhpPermitRange' => [
                            'id' => 101,
                            'countrys' => [
                            ]
                        ]
                    ],
                    [
                        'id' => 464,
                        'irhpPermitRange' => [
                            'id' => 102,
                            'countrys' => [
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia'],
                            ]
                        ]
                    ],
                    [
                        'id' => 465,
                        'irhpPermitRange' => [
                            'id' => 103,
                            'countrys' => [
                                ['id' => 'AT', 'countryDesc' => 'Austria'],
                                ['id' => 'GR', 'countryDesc' => 'Greece'],
                                ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                ['id' => 'IT', 'countryDesc' => 'Italy'],
                                ['id' => 'RU', 'countryDesc' => 'Russia'],
                            ]
                        ]
                    ],
                ],
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'expectedRestrictedCountriesFromConfig' => [],
                'restrictedCountries' => [],
                'expectedResult' => [
                    'results' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                                'countrys' => [
                                ]
                            ],
                            'permitNumber' => 1,
                            'constrainedCountries' => [],
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                                'countrys' => [
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 2,
                            'constrainedCountries' => [],
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                                'countrys' => [
                                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                                    ['id' => 'RU', 'countryDesc' => 'Russia']
                                ]
                            ],
                            'permitNumber' => 3,
                            'constrainedCountries' => [],
                        ]
                    ],
                    'count' => 3
                ],
            ],
        ];
    }
}
