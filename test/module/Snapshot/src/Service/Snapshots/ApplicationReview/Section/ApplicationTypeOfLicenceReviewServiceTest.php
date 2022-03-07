<?php

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationTypeOfLicenceReviewService;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceReviewServiceTest extends \PHPUnit\Framework\TestCase
{
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new ApplicationTypeOfLicenceReviewService();
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    'niFlag' => 'N',
                    'isGoods' => true,
                    'goodsOrPsv' => [
                        'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'lgvDeclarationConfirmation' => false,
                    'licenceType' => [
                        'description' => 'Standard National'
                    ],
                    'vehicleType' => [
                        'id' => RefData::APP_VEHICLE_TYPE_HGV
                    ],
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Great Britain'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-operator-type',
                                'value' => 'Goods'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard National'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'N',
                    'isGoods' => false,
                    'goodsOrPsv' => [
                        'id' => Licence::LICENCE_CATEGORY_PSV
                    ],
                    'lgvDeclarationConfirmation' => false,
                    'licenceType' => [
                        'description' => 'Standard International'
                    ],
                    'vehicleType' => [
                        'id' => RefData::APP_VEHICLE_TYPE_MIXED
                    ],
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Great Britain'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-operator-type',
                                'value' => 'PSV'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard International'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-vehicle-type',
                                'value' => 'No'
                            ]
                        ],
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'N',
                    'isGoods' => false,
                    'goodsOrPsv' => [
                        'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'lgvDeclarationConfirmation' => true,
                    'licenceType' => [
                        'description' => 'Standard International'
                    ],
                    'vehicleType' => [
                        'id' => RefData::APP_VEHICLE_TYPE_LGV
                    ],
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Great Britain'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-operator-type',
                                'value' => 'PSV'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard International'
                            ],
                            [
                                'label' => 'application-review-type-of-licence-vehicle-type',
                                'value' => 'Yes'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-lgv-declaration-confirmation',
                                'value' => 'Confirmed'
                            ]
                        ],
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'Y',
                    'licenceType' => [
                        'description' => 'Standard National'
                    ],
                    'vehicleType' => [
                        'id' => RefData::APP_VEHICLE_TYPE_HGV
                    ],
                ],
                [
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-type-of-licence-operator-location',
                                'value' => 'Northern Ireland'
                            ]
                        ],
                        [
                            [
                                'label' => 'application-review-type-of-licence-licence-type',
                                'value' => 'Standard National'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
