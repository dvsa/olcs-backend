<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit\GetListByEcmtId as GetListHandler;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByEcmtId as GetListQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Mockery as m;
use Doctrine\ORM\Query;

class GetListByEcmtIdTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(GetListHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

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
            'replaces',
            'irhpPermitRange' => [
                'countrys' => [
                    'country'
                ],
                'emissionsCategory',
            ]
        ];

        $bundledIrhpPermits = [
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
            ->with($irhpPermits, $expectedBundle)
            ->andReturn($bundledIrhpPermits);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResult, $result);
    }
}
