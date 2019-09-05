<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit\GetList as GetListHandler;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetList as GetListQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Mockery as m;
use Doctrine\ORM\Query;

class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(GetListHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = GetListQry::create([]);

        $restrictedCountries = [
            'result' => [
                ['id' => 'AT', 'name' => 'Austria'],
                ['id' => 'GR', 'name' => 'Greece'],
                ['id' => 'HU', 'name' => 'Hungary'],
                ['id' => 'IT', 'name' => 'Italy'],
                ['id' => 'RU', 'name' => 'Russia']
            ]
        ];

        $queryHandler = m::mock(AbstractQueryHandler::class);
        $queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($restrictedCountries) {
                $this->assertInstanceOf(EcmtConstrainedCountriesList::class, $query);
                $this->assertEquals(1, $query->hasEcmtConstraints());

                return $restrictedCountries;
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

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

        $bundledIrhpCandidatePermits = [
            [
                'id' => 463,
                'irhpPermitRange' => [
                    'countrys' => [
                    ]
                ]
            ],
            [
                'id' => 464,
                'irhpPermitRange' => [
                    'countrys' => [
                        ['id' => 'HU', 'name' => 'Hungary'],
                        ['id' => 'IT', 'name' => 'Italy'],
                        ['id' => 'RU', 'name' => 'Russia']
                    ]
                ]
            ],
            [
                'id' => 465,
                'irhpPermitRange' => [
                    'countrys' => [
                        ['id' => 'AT', 'name' => 'Austria'],
                        ['id' => 'GR', 'name' => 'Greece'],
                        ['id' => 'HU', 'name' => 'Hungary'],
                        ['id' => 'IT', 'name' => 'Italy'],
                        ['id' => 'RU', 'name' => 'Russia']
                    ]
                ]
            ],
        ];

        $expectedResult = [
            'result' => [
                [
                    'id' => 463,
                    'irhpPermitRange' => [
                        'countrys' => [
                        ]
                    ],
                    'constrainedCountries' => [
                        ['id' => 'AT', 'name' => 'Austria'],
                        ['id' => 'GR', 'name' => 'Greece'],
                        ['id' => 'HU', 'name' => 'Hungary'],
                        ['id' => 'IT', 'name' => 'Italy'],
                        ['id' => 'RU', 'name' => 'Russia']
                    ]
                ],
                [
                    'id' => 464,
                    'irhpPermitRange' => [
                        'countrys' => [
                            ['id' => 'HU', 'name' => 'Hungary'],
                            ['id' => 'IT', 'name' => 'Italy'],
                            ['id' => 'RU', 'name' => 'Russia']
                        ]
                    ],
                    'constrainedCountries' => [
                        ['id' => 'AT', 'name' => 'Austria'],
                        ['id' => 'GR', 'name' => 'Greece']
                    ]
                ],
                [
                    'id' => 465,
                    'irhpPermitRange' => [
                        'countrys' => [
                            ['id' => 'AT', 'name' => 'Austria'],
                            ['id' => 'GR', 'name' => 'Greece'],
                            ['id' => 'HU', 'name' => 'Hungary'],
                            ['id' => 'IT', 'name' => 'Italy'],
                            ['id' => 'RU', 'name' => 'Russia']
                        ]
                    ],
                    'constrainedCountries' => [
                    ]
                ]
            ],
            'count' => 3
        ];

        $this->sut->shouldReceive('resultList')
            ->with($irhpCandidatePermits, $expectedBundle)
            ->andReturn($bundledIrhpCandidatePermits);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }
}
