<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit\GetListByLicence as Handler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence as GetListByLicenceQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * GetListByLicence Test
 */
class GetListByLicenceTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = m::mock(Handler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        $this->mockedSmServices['PermitsCommonRangeBasedRestrictedCountriesProvider']
            = m::mock(RangeBasedRestrictedCountriesProvider::class);

        parent::setUp();
    }

    public function testHandleQueryForEcmtShortTerm()
    {
        $query = GetListByLicenceQuery::create(
            [
                'licence' => 10,
                'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'page' => 1,
                'limit' => 25,
            ]
        );

        $irhpPermits = [
            m::mock(IrhpPermit::class),
            m::mock(IrhpPermit::class),
            m::mock(IrhpPermit::class)
        ];
        $irhpPermitsCount = 3;

        $this->repoMap['IrhpPermit']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermits);
        $this->repoMap['IrhpPermit']->shouldReceive('fetchCount')
            ->andReturn($irhpPermitsCount);

        $expectedBundle = [
            'irhpPermitApplication',
            'irhpPermitRange' => [
                'journey',
                'irhpPermitStock' => [
                    'irhpPermitType' => ['name'],
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $bundledIrhpPermits = [
            [
                'id' => 463,
                'irhpPermitRange' => [
                    'id' => 101,
                ]
            ],
            [
                'id' => 464,
                'irhpPermitRange' => [
                    'id' => 102,
                ]
            ],
            [
                'id' => 465,
                'irhpPermitRange' => [
                    'id' => 103,
                ]
            ],
        ];

        $this->sut->shouldReceive('resultList')
            ->with($irhpPermits, $expectedBundle)
            ->andReturn($bundledIrhpPermits);

        $this->mockedSmServices['PermitsCommonRangeBasedRestrictedCountriesProvider']
            ->shouldReceive('getList')
            ->with(101)
            ->andReturn(
                [
                    ['id' => 'AT', 'countryDesc' => 'Austria'],
                    ['id' => 'GR', 'countryDesc' => 'Greece'],
                    ['id' => 'HU', 'countryDesc' => 'Hungary'],
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ]
            )
            ->shouldReceive('getList')
            ->with(102)
            ->andReturn(
                [
                    ['id' => 'IT', 'countryDesc' => 'Italy'],
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ]
            )
            ->shouldReceive('getList')
            ->with(103)
            ->andReturn(
                [
                    ['id' => 'RU', 'countryDesc' => 'Russia'],
                ]
            );

        $expectedResult = [
            'results' => [
                [
                    'id' => 463,
                    'irhpPermitRange' => [
                        'id' => 101,
                    ],
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
                    ],
                    'constrainedCountries' => [
                        ['id' => 'IT', 'countryDesc' => 'Italy'],
                        ['id' => 'RU', 'countryDesc' => 'Russia']
                    ]
                ],
                [
                    'id' => 465,
                    'irhpPermitRange' => [
                        'id' => 103,
                    ],
                    'constrainedCountries' => [
                        ['id' => 'RU', 'countryDesc' => 'Russia']
                    ]
                ]
            ],
            'count' => 3
        ];

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    public function testHandleQueryForEcmtShortTermWithEmptyList()
    {
        $query = GetListByLicenceQuery::create(
            [
                'licence' => 10,
                'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'page' => 1,
                'limit' => 25,
            ]
        );

        $irhpPermits = [];
        $irhpPermitsCount = 0;

        $this->repoMap['IrhpPermit']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermits);
        $this->repoMap['IrhpPermit']->shouldReceive('fetchCount')
            ->andReturn($irhpPermitsCount);

        $expectedBundle = [
            'irhpPermitApplication',
            'irhpPermitRange' => [
                'journey',
                'irhpPermitStock' => [
                    'irhpPermitType',
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $bundledIrhpPermits = [];

        $this->sut->shouldReceive('resultList')
            ->with($irhpPermits, $expectedBundle)
            ->andReturn($bundledIrhpPermits);

        $this->mockedSmServices['PermitsCommonRangeBasedRestrictedCountriesProvider']
            ->shouldReceive('getList')
            ->never();

        $expectedResult = [
            'results' => [],
            'count' => 0
        ];

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider dpTestHandleQueryForOtherPermitTypes
     */
    public function testHandleQueryForOtherPermitTypes($irhpPermitTypeId)
    {
        $query = GetListByLicenceQuery::create(
            [
                'licence' => 10,
                'irhpPermitType' => $irhpPermitTypeId,
                'page' => 1,
                'limit' => 25,
            ]
        );

        $irhpPermits = [
            m::mock(IrhpPermit::class),
            m::mock(IrhpPermit::class),
            m::mock(IrhpPermit::class)
        ];
        $irhpPermitsCount = 3;

        $this->repoMap['IrhpPermit']->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermits);
        $this->repoMap['IrhpPermit']->shouldReceive('fetchCount')
            ->andReturn($irhpPermitsCount);

        $expectedBundle = [
            'irhpPermitApplication',
            'irhpPermitRange' => [
                'journey',
                'irhpPermitStock' => [
                    'irhpPermitType' => ['name'],
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $bundledIrhpPermits = [
            [
                'id' => 463,
                'irhpPermitRange' => [
                    'id' => 101,
                ]
            ],
            [
                'id' => 464,
                'irhpPermitRange' => [
                    'id' => 102,
                ]
            ],
            [
                'id' => 465,
                'irhpPermitRange' => [
                    'id' => 103,
                ]
            ],
        ];

        $this->sut->shouldReceive('resultList')
            ->with($irhpPermits, $expectedBundle)
            ->andReturn($bundledIrhpPermits);

        $this->mockedSmServices['PermitsCommonRangeBasedRestrictedCountriesProvider']
            ->shouldReceive('getList')
            ->never();

        $expectedResult = [
            'results' => [
                [
                    'id' => 463,
                    'irhpPermitRange' => [
                        'id' => 101,
                    ],
                ],
                [
                    'id' => 464,
                    'irhpPermitRange' => [
                        'id' => 102,
                    ],
                ],
                [
                    'id' => 465,
                    'irhpPermitRange' => [
                        'id' => 103,
                    ],
                ]
            ],
            'count' => 3
        ];

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    public function dpTestHandleQueryForOtherPermitTypes()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
        ];
    }
}
