<?php

/**
 * Application Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationPsvOcTotalAuthReviewService;

/**
 * Application Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvOcTotalAuthReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationPsvOcTotalAuthReviewService();
    }

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testGetConfigFromData($licenceType, $expected)
    {
        $data = [
            'licenceType' => ['id' => $licenceType],
            'totAuthVehicles' => 60,
            'totCommunityLicences' => 50
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function licenceTypeProvider()
    {
        return [
            'standard national' => [
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ]
                        ]
                    ]
                ]
            ],
            'standard international' => [
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 50
                            ]
                        ]
                    ]
                ]
            ],
            'restricted' => [
                Licence::LICENCE_TYPE_RESTRICTED,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 50
                            ]
                        ]
                    ]
                ]
            ],
            'special restricted' => [
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}
