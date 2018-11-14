<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\GetScoredPermitList as GetScoredListHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * GetScoredList Test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.com>
 */
class GetScoredPermitListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(GetScoredListHandler::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = QryClass::create([ 'stockId' => 1]);

        $firstPermitId = '1';
        $interJourneysLess60 = 'inter_journey_less_60';
        $firstIrhpApplicationId = '101';
        $firstLicenceNo = 'OB1111';
        $firstOrganisationName = 'Testing Inc.';
        $firstAppScore = '1.1';
        $firstIntensity = '0.8';
        $firstRandomFactor = '0.2';
        $firstRandomizedScore = '2.9';
        $firstSectorsName = 'TEST';
        $firstTrafficAreaName = 'Ireland';
        $firstApplicationRef = 'OB1111 / 101';
        $expectedRefNum = 'OB1111 / 101 / 1';

        $secondSectorName = 'None/More than one of these sectors';

        $rawResults = [
            0 => [
                'id' => $firstPermitId,
                'applicationScore' => $firstAppScore,
                'intensityOfUse' => $firstIntensity,
                'randomFactor' => $firstRandomFactor,
                'randomizedScore' => $firstRandomizedScore,
                'successful' => 1,
                'irhpPermitApplication' => [
                    'id' => $firstIrhpApplicationId,
                    'ecmtPermitApplication' => [
                        'applicationRef' => $firstApplicationRef,
                        'sectors' => [
                            'name' => $firstSectorsName
                        ],
                        'internationalJourneys' => [
                            'id' => $interJourneysLess60
                        ],
                        'hasRestrictedCountries' => false //don't need to specify countries because this is false
                    ],
                    'licence' => [
                        'licNo' => $firstLicenceNo,
                        'organisation' => [
                            'name' => $firstOrganisationName
                        ],
                        'trafficArea' => [
                            'id' => 'M', // this needs to match a Devolved Administration Traffic Area
                            'name' => $firstTrafficAreaName
                        ]
                    ]
                ],
                'irhpPermitRange' => [
                    'countrys' => []
                ]
            ],
            1 => [
                'id' => $firstPermitId,
                'applicationScore' => $firstAppScore,
                'intensityOfUse' => $firstIntensity,
                'randomFactor' => $firstRandomFactor,
                'randomizedScore' => $firstRandomizedScore,
                'successful' => 0,
                'irhpPermitApplication' => [
                    'id' => $firstIrhpApplicationId,
                    'ecmtPermitApplication' => [
                        'applicationRef' => $firstApplicationRef,
                        'sectors' => [
                            'name' => $secondSectorName
                        ],
                        'internationalJourneys' => [
                            'id' => $interJourneysLess60
                        ],
                        'hasRestrictedCountries' => 1, //need to specify countries because this is true
                        'countrys' => [
                            0 => ['countryDesc' => 'Cuba'],
                            1 => ['countryDesc' => 'USA']
                        ]
                    ],
                    'licence' => [
                        'licNo' => $firstLicenceNo,
                        'organisation' => [
                            'name' => $firstOrganisationName
                        ],
                        'trafficArea' => [
                            'id' => 'X', // this needs to NOT match a Devolved Administration Traffic Area
                            'name' => $firstTrafficAreaName
                        ]
                    ]
                ],
                'irhpPermitRange' => [
                    'countrys' => [
                        0 => ['countryDesc' => 'England'],
                        1 => ['countryDesc' => 'France']
                    ]
                ]
            ],
        ];

        $this->sut->shouldReceive('resultList')
            ->once()
            ->with(
                $rawResults,
                [
                    'irhpPermitApplication' => [
                        'ecmtPermitApplication' => [
                            'countrys',
                            'sectors',
                            'internationalJourneys'
                        ],
                        'irhpPermitWindow',
                        'licence' => [
                            'trafficArea',
                            'organisation'
                        ]
                    ],
                    'irhpPermitRange' => [
                        'countrys'
                    ],
                ]
            )
            ->andReturn($rawResults);

        $this->repoMap['IrhpCandidatePermit']
            ->shouldReceive('fetchAllScoredForStock')
            ->with($query->getStockId())
            ->once()
            ->andReturn($rawResults);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                0 => [
                    'Permit Ref' => $expectedRefNum,
                    'Operator' => $firstOrganisationName,
                    'Application Score' => $firstAppScore,
                    'Permit Intensity of Use' => $firstIntensity,
                    'Random Factor' => $firstRandomFactor,
                    'Randomised Permit Score' => $firstRandomizedScore,
                    'Percentage International' => 0.3, //map for less than 60
                    'Sector' => $firstSectorsName,
                    'Devolved Administration' => $firstTrafficAreaName,
                    'Result' => 'Successful',
                    'Restricted Countries - Requested' => 'N/A',
                    'Restricted Countries - Offered' => 'N/A',
                ],
                1 => [
                    'Permit Ref' => $expectedRefNum,
                    'Operator' => $firstOrganisationName,
                    'Application Score' => $firstAppScore,
                    'Permit Intensity of Use' => $firstIntensity,
                    'Random Factor' => $firstRandomFactor,
                    'Randomised Permit Score' => $firstRandomizedScore,
                    'Percentage International' => 0.3, //less than 60%
                    'Sector' => 'N/A',
                    'Devolved Administration' => 'N/A',
                    'Result' => 'Unsuccessful',
                    'Restricted Countries - Requested' => 'Cuba; USA',
                    'Restricted Countries - Offered' => 'England; France',
                ],
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testHandleQueryNoResults()
    {
        $query = QryClass::create([ 'stockId' => 1]);

        $rawResults = [];
        $expected = ['result' => []];

        $this->sut->shouldReceive('resultList')
            ->once()
            ->with(
                $rawResults,
                [
                    'irhpPermitApplication' => [
                        'ecmtPermitApplication' => [
                            'countrys',
                            'sectors',
                            'internationalJourneys'
                        ],
                        'irhpPermitWindow',
                        'licence' => [
                            'trafficArea',
                            'organisation'
                        ]
                    ],
                    'irhpPermitRange' => [
                        'countrys'
                    ],
                ]
            )
            ->andReturn($rawResults);

        $this->repoMap['IrhpCandidatePermit']
            ->shouldReceive('fetchAllScoredForStock')
            ->with($query->getStockId())
            ->once()
            ->andReturn($rawResults);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals($expected, $result);
    }
}
