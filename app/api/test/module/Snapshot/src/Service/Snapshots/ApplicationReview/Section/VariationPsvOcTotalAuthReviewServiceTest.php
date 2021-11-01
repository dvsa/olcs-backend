<?php

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationPsvOcTotalAuthReviewService;

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvOcTotalAuthReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->with('review-value-decreased')
            ->andReturn('decreased from %s to %s')
            ->shouldReceive('translate')
            ->with('review-value-increased')
            ->andReturn('increased from %s to %s');

        $this->sm->setService('translator', $mockTranslator);

        $this->sut = new VariationPsvOcTotalAuthReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider dpGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function dpGetConfigFromData()
    {
        return [
            'without changes' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'totAuthVehicles' => 10,
                    'licence' => [
                        'totAuthVehicles' => 10,
                    ]
                ],
                'expected' => null,
            ],
            'with changes' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'totAuthVehicles' => 10,
                    'licence' => [
                        'totAuthVehicles' => 20,
                    ]
                ],
                'expected' => [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 'decreased from 20 to 10'
                            ]
                        ]
                    ]
                ],
            ],
            'with changes - standard international' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
                    'totAuthVehicles' => 10,
                    'totCommunityLicences' => 5,
                    'licence' => [
                        'totAuthVehicles' => 20,
                        'totCommunityLicences' => 1,
                        ]
                ],
                'expected' => [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 'decreased from 20 to 10'
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 'increased from 1 to 5'
                            ]
                        ]
                    ]
                ],
            ],
            'with changes - restricted' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_RESTRICTED],
                    'totAuthVehicles' => 10,
                    'totCommunityLicences' => 5,
                    'licence' => [
                        'totAuthVehicles' => 20,
                        'totCommunityLicences' => 1,
                        ]
                ],
                'expected' => [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 'decreased from 20 to 10'
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 'increased from 1 to 5'
                            ]
                        ]
                    ]
                ],
            ],
            'with changes to zero' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'totAuthVehicles' => 0,
                    'licence' => [
                        'totAuthVehicles' => 20,
                    ]
                ],
                'expected' => [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 'decreased from 20 to 0'
                            ]
                        ]
                    ]
                ],
            ],
            'with changes to zero - standard international' => [
                'data' => [
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
                    'totAuthVehicles' => 0,
                    'totCommunityLicences' => 0,
                    'licence' => [
                        'totAuthVehicles' => 20,
                        'totCommunityLicences' => 1,
                        ]
                ],
                'expected' => [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 'decreased from 20 to 0'
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 'decreased from 1 to 0'
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}
