<?php

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationTypeOfLicenceReviewService;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Application Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceReviewServiceTest extends \PHPUnit\Framework\TestCase
{
    protected $sut;

    public function setUp()
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
                    'licenceType' => [
                        'description' => 'Standard National'
                    ]
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
                    'licenceType' => [
                        'description' => 'Standard International'
                    ]
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
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'niFlag' => 'Y',
                    'licenceType' => [
                        'description' => 'Standard International'
                    ]
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
                                'value' => 'Standard International'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
