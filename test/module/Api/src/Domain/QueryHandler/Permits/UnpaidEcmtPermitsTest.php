<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\UnpaidEcmtPermits as UnpaidEcmtPermitsHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\Permits\UnpaidEcmtPermits as UnpaidEcmtPermitsQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * UnpaidEcmtPermits Test
 */
class UnpaidEcmtPermitsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(UnpaidEcmtPermitsHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleQueryWithEmptyList()
    {
        $query = UnpaidEcmtPermitsQry::create(
            [
                'id' => 10,
                'page' => 1,
                'limit' => 25,
                'status' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
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
            'result' => [],
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
        $expectedResult
    ) {
        $query = UnpaidEcmtPermitsQry::create($queryData);

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

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }

    public function dpTestHandleQuery()
    {
        return [
            'page 1' => [
                'queryData' => [
                    'id' => 10,
                    'page' => 1,
                    'limit' => 25,
                    'status' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                'bundledIrhpCandidatePermits' => [
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
                ],
                'expectedResult' => [
                    'result' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                            ],
                            'permitNumber' => 1,
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                            ],
                            'permitNumber' => 2,
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                            ],
                            'permitNumber' => 3,
                        ]
                    ],
                    'count' => 3
                ],
            ],
            'page 3' => [
                'queryData' => [
                    'id' => 10,
                    'page' => 3,
                    'limit' => 25,
                    'status' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                'bundledIrhpCandidatePermits' => [
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
                ],
                'expectedResult' => [
                    'result' => [
                        [
                            'id' => 463,
                            'irhpPermitRange' => [
                                'id' => 101,
                            ],
                            'permitNumber' => 51,
                        ],
                        [
                            'id' => 464,
                            'irhpPermitRange' => [
                                'id' => 102,
                            ],
                            'permitNumber' => 52,
                        ],
                        [
                            'id' => 465,
                            'irhpPermitRange' => [
                                'id' => 103,
                            ],
                            'permitNumber' => 53,
                        ]
                    ],
                    'count' => 3
                ],
            ],
        ];
    }
}
