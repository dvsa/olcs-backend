<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\GetScoredPermitList as GetScoredListHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList as QryClass;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;

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

    public function testHandleQueryMapping()
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

      $secondTrafficAreaName = 'Rule Britannia';
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
                'permitRef' => $firstLicenceNo . '/' . $firstIrhpApplicationId . '/' . $firstPermitId,
                'organisation' => $firstOrganisationName,
                'applicationScore' => $firstAppScore,
                'intensityOfUse' => $firstIntensity,
                'randomFactor' => $firstRandomFactor,
                'randomizedScore' => $firstRandomizedScore,
                'internationalJourneys' => EcmtPermitApplicationEntity::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$interJourneysLess60],
                'sector' => $firstSectorsName,
                'devolvedAdministration' => $firstTrafficAreaName,
                'result' => 'Successful',
                'restrictedCountriesRequested' => 'N/A',
                'restrictedCountriesOffered' => 'N/A',
              ],
              1 => [
                'permitRef' => $firstLicenceNo . '/' . $firstIrhpApplicationId . '/' . $firstPermitId,
                'organisation' => $firstOrganisationName,
                'applicationScore' => $firstAppScore,
                'intensityOfUse' => $firstIntensity,
                'randomFactor' => $firstRandomFactor,
                'randomizedScore' => $firstRandomizedScore,
                'internationalJourneys' => EcmtPermitApplicationEntity::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$interJourneysLess60],
                'sector' => 'N/A',
                'devolvedAdministration' => 'N/A',
                'result' => 'Unsuccessful',
                'restrictedCountriesRequested' => 'Cuba; USA',
                'restrictedCountriesOffered' => 'England; France',
              ],
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}
