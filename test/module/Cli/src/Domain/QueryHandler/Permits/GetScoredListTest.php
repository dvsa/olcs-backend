<?php

namespace Dvsa\OlcsTest\Cli\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Cli\Domain\QueryHandler\Permits\GetScoredList;
use Dvsa\Olcs\Cli\Domain\Query\Permits\GetScoredList as GetScoredListQuery;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Scored irhp candidate permit list test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.com>
 */
class GetScoredListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StockLackingRandomisedScore();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $stockId = 1;
        $trafficAreaName = 

        $rawResultList = [
            0 => [
                'id' => 1,
                'applicationScore' => 0.8,
                'intensityOfUse' => 1.2,
                'randomFactor' => 3,
                'randomizedScore' => 12.2222,
                'successful' => 1,
                'irhpPermitRange' => [
                    'countrys' => []
                ],
                'irhpPermitApplication' => [
                    'id' => 101,
                    'ecmtPermitApplication' => [
                        'hasRestrictedCountries' => 0,
                        'countrys' => [],
                        'sectors' => [
                            'name' => 'nuclear warheads'
                        ],
                        'internationalJourneys' => [
                            'id' => 3
                        ]
                    ],
                    'licence' => [
                        'licNo' => 'OB111111',
                        'organisation' => [
                            'name' => 'petridge farm ltd'
                        ],
                        'trafficArea' => [
                            'id' => 'G',
                            'name' => 'Atlantis'
                        ]
                    ]
                ]
            ]
        ];

        $expectedResult = [
            'permitRef' =>
            'organisation'
            'intensityOfUse'
            'randomFactor'
            'randomizedScore'
            'internationalJourneys'
            'sector' =>
            'devolvedAdministration' => 'Atlantis'
            'result' => 'Successful',
            'restrictedCountriesRequested' => 'N/A',
            'restrictedCountriesOffered' => 'N/A'
        ]

        'permitRef'                     => $row['irhpPermitApplication']['licence']['licNo'] . '/' . $row['irhpPermitApplication']['id'] . '/' . $row['id'],
                'organisation'                  => $row['irhpPermitApplication']['licence']['organisation']['name'],
                'applicationScore'              => $row['applicationScore'],
                'intensityOfUse'                => $row['intensityOfUse'],
                'randomFactor'                  => $row['randomFactor'],
                'randomizedScore'               => $row['randomizedScore'],
                'internationalJourneys'         => EcmtPermitApplication::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$row['irhpPermitApplication']['ecmtPermitApplication']['internationalJourneys']['id']],
                'sector'                        => $sector['name'] === 'None/More than one of these sectors' ? 'N/A' : $sector['name'],
                'devolvedAdministration'        => in_array(
                    $row['irhpPermitApplication']['licence']['trafficArea']['id'],
                    self::DEVOLVED_ADMINISTRATION_TRAFFIC_AREAS
                ) ? $row['irhpPermitApplication']['licence']['trafficArea']['name'] : 'N/A',
                'result'                        => $row['successful'] ? 'Successful' : 'Unsuccessful',
                'restrictedCountriesRequested'  => self::getRestrictedCountriesRequested($row),
                'restrictedCountriesOffered'    => self::getRestrictedCountriesOffered($row)
            ];

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchAllScoredForStock')
            ->with($stockId)
            ->andReturn($rawResultList);

        $query = m::mock(GetScoredListQuery::class);
        $query->shouldReceive('getStockId')
            ->andReturn($stockId);

        $this->assertEquals(
            ['result' => $result],
            $this->sut->handleQuery($query)
        );
    }
}
