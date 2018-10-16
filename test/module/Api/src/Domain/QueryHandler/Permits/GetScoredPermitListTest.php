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
        $this->sut = new GetScoredListHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
      $firstPermitId = '1';
      $interJourneysLess60 = 'inter_journey_less_60';
        $firstIrhpApplicationId = '101';
        $firstLicenceNo = 'OB1111';
        $firstOrganisationName = 'Testing Inc.';
        $firstAppScore = '1.1';
        $firstIntensity = '0.8';
        $firstRandomFactor = '0.2';
        $firstRandomizedScore = '2.9';

        $secondTrafficAreaName = 'Rule Britannia';
        $secondSectorName = 'Some Sector Test';


        $rawResults = [
            'results' => [
                0 => [
                    'id' => $firstPermitId,
                    'applicationScore' => $firstAppScore,
                    'intensityOfUse' => $firstIntensity,
                    'randomFactor' => $firstRandomFactor,
                    'randomizedScore' => $firstRandomizedScore,
                    'successful' => true,
                    'irhpPermitApplication' => [
                        'id' => $firstIrhpApplicationId,
                        'ecmtPermitApplication' => [
                            'sectors' => [
                                'name' => 'None/More than one of these sectors'
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
                                'id' => 'X',
                                'name' => ''
                            ]
                        ]
                    ]
                ],
                1 => [
                    'id' => $firstPermitId,
                    'applicationScore' => $firstAppScore,
                    'intensityOfUse' => $firstIntensity,
                    'randomFactor' => $firstRandomFactor,
                    'randomizedScore' => $firstRandomizedScore,
                    'successful' => true,
                    'irhpPermitApplication' => [
                        'id' => $firstIrhpApplicationId,
                        'ecmtPermitApplication' => [
                            'sectors' => [
                                'name' => $secondSectorName
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
                                'id' => 'M',
                                'name' => $secondTrafficAreaName
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $query = QryClass::create([ 'stockId' => 1]);
//'irhpPermitApplication']['ecmtPermitApplication']['internationalJourneys']['id']
        /*$item1 = m::mock(IrhpCandidatePermitEntity::class)->makePartial();
        $irhpApp1 = m::mock(IrhpPermitApplicationEntity::class)->makePartial();
        $ecmtApp1 = m::mock(EcmtPermitApplicationEntity::class)->makePartial();
        $interJourneys1 = m::mock(RefDataEntity::class)->makePartial();
        $status1 = m::mock(RefDataEntity::class)->makePartial();
        $licence1 = m::mock(LicenceEntity::class)->makePartial();

        $interJourneys1->setId($interJourneysLess60);
        $status1->setId('pemrit_app_nys');
        $licence1->setLicNo($firstLicenceNo);
        $ecmtApp1->setInternationalJourneys($interJourneys1);
        $ecmtApp1->setCountrys(new ArrayCollection());
        $ecmtApp1->setLicence($licence1);
        $ecmtApp1->setStatus($status1);
        $irhpApp1->setEcmtPermitApplication($ecmtApp1);
        $item1->setIrhpPermitApplication($irhpApp1);

        $item2 = m::mock(IrhpCandidatePermitEntity::class)->makePartial();
        $scoredPermits = new ArrayCollection();

        $item1->setId($firstPermitId);

        $scoredPermits->add($item1);
       // $scoredPermits->add($item2);*/

        $this->sut = m::mock(GetScoredListHandler::class)->makePartial()->shouldAllowMockingProtectedMethods();
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

       /* $expected = [
            'result' => [
                0 => [],
                1 => []
            ]
        ];

        $this->assertArraySubset($expected, $result);*/
    }
}
