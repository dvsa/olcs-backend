<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\LicenceChecklist;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist as LicenceChecklistQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Service\Lva\SectionAccessService;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;

class LicenceChecklistTest extends QueryHandlerTestCase
{
    /** @var  LicenceChecklist */
    protected $sut;

    public function setUp()
    {
        $this->sut = new LicenceChecklist();

        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);
        $this->mockedSmServices = [
            'SectionAccessService' => m::mock(SectionAccessService::class),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $mockLicence = m::mock(LicenceEntity::class)
            ->shouldReceive('getConditionUndertakings')
            ->andReturn([])
            ->once()
            ->shouldReceive('getOcPendingChanges')
            ->andReturn(1)
            ->once()
            ->shouldReceive('getTmPendingChanges')
            ->andReturn(2)
            ->once()
            ->getMock();

        /** @var ContinuationDetailEntity $continuationDetail */
        $mockContinuationDetail = m::mock(ContinuationDetailEntity::class)
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->once()
            ->shouldReceive('serialize')
            ->andReturn(
                [
                    'licence' => [
                        'licenceType' => 'expected',
                        'status' => 'expected',
                        'goodsOrPsv' => 'expected',
                        'trafficArea' => 'expected',
                        'organisation' => [
                            'type' => 'expected',
                            'organisationPersons' => [
                                'person' => [
                                    'title' => 'expected'
                                ]
                            ]
                        ],
                        'tradingNames',
                        'licenceVehicles' => [
                            'vehicle' => 'expected'
                        ],
                        'correspondenceCd' => [
                            'address',
                            'phoneContacts' => [
                                'phoneContactType',
                            ],
                        ],
                        'establishmentCd' => [
                            'address',
                        ],
                        'operatingCentres' => [
                            'operatingCentre' => [
                                'address'
                            ]
                        ],
                        'tmLicences' => [
                            'transportManager' => [
                                'homeCd' => [
                                    'person' => [
                                        'title'
                                    ]
                                ]
                            ]
                        ],
                        'workshops' => [
                            'contactDetails' => [
                                'person' => [
                                    'title'
                                ],
                                'address'
                            ]
                        ],
                        'tachographIns'
                    ],
                    'sections' => [
                        'fooBar',
                    ],
                    'ocChanges' => 1,
                    'tmChanges' => 2,
                ]
            )
            ->getMock();

        $this->mockedSmServices['SectionAccessService']
            ->shouldReceive('getAccessibleSectionsForLicence')
            ->with($mockLicence)
            ->andReturn(['foo_bar' => 'cake', 'conditions_undertakings' => 'cake'])
            ->once()
            ->getMock();

        $query = LicenceChecklistQry::create(['id' => 999]);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchWithLicence')
            ->with(999)
            ->once()
            ->andReturn($mockContinuationDetail);

        $expected = [
            'licence' => [
                'licenceType' => 'expected',
                'status' => 'expected',
                'goodsOrPsv' => 'expected',
                'trafficArea' => 'expected',
                'organisation' => [
                    'type' => 'expected',
                    'organisationPersons' => [
                        'person' => [
                            'title' => 'expected'
                        ]
                    ]
                ],
                'tradingNames',
                'licenceVehicles' => [
                    'vehicle' => 'expected'
                ],
                'correspondenceCd' => [
                    'address',
                    'phoneContacts' => [
                        'phoneContactType',
                    ],
                ],
                'establishmentCd' => [
                    'address',
                ],
                'operatingCentres' => [
                    'operatingCentre' => [
                        'address'
                    ]
                ],
                'tmLicences' => [
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'title'
                            ]
                        ]
                    ]
                ],
                'workshops' => [
                    'contactDetails' => [
                        'person' => [
                            'title'
                        ],
                        'address'
                    ]
                ],
                'tachographIns'
            ],
            'sections' => [
                'fooBar',
            ],
            'ocChanges' => 1,
            'tmChanges' => 2,
        ];
        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
