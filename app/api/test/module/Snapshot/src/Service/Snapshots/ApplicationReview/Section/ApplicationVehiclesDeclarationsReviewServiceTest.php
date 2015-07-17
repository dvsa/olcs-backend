<?php

/**
 * Application Vehicles Declarations Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationVehiclesDeclarationsReviewService;

/**
 * Application Vehicles Declarations Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesDeclarationsReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationVehiclesDeclarationsReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            // Non Scottish, Non restricted licence, with small vehicles, with novelty vehicles
            [
                [
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'Y',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => false
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-small-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15b' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15b1',
                                                'value' => 'Yes'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15b2',
                                                'noEscape' => true,
                                                'value' => "Foo<br />\nBar"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        '15g' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15g',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Non Scottish, Non restricted licence, with small vehicles, without novelty vehicles
            [
                [
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => false
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-small-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15b' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15b1',
                                                'value' => 'Yes'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15b2',
                                                'noEscape' => true,
                                                'value' => "Foo<br />\nBar"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Non Scottish, Non restricted licence, with small vehicles, without novelty vehicles, with conditions
            // and undertakings
            [
                [
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'N',
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => false
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-small-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15b' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15b1',
                                                'value' => 'No'
                                            ],
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15cd',
                                                'value' => 'Confirmed'
                                            ],
                                        ],
                                        [
                                            [
                                                'full-content' => 'markup-application_vehicle-safety_undertakings-'
                                                    . 'smallVehiclesUndertakingsScotland-translated',
                                            ]
                                        ],
                                        [
                                            [
                                                'full-content' => '<h4>Undertakings</h4>'
                                                    . 'markup-application_vehicle-safety_undertakings-'
                                                    . 'smallVehiclesUndertakings-translated'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Scottish, Non restricted licence, with small vehicles, without novelty vehicles, with conditions
            // and undertakings
            [
                [
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-small-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15cd',
                                                'value' => 'Confirmed'
                                            ],
                                        ],
                                        [
                                            [
                                                'full-content' => 'markup-application_vehicle-safety_undertakings-'
                                                    . 'smallVehiclesUndertakingsScotland-translated',
                                            ]
                                        ],
                                        [
                                            [
                                                'full-content' => '<h4>Undertakings</h4>'
                                                    . 'markup-application_vehicle-safety_undertakings-'
                                                    . 'smallVehiclesUndertakings-translated'
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Scottish, Non restricted licence, without small vehicles, with large vehicles, without novelty vehicles
            [
                [
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 0,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-medium-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15e',
                                                'value' => 'Confirmed'
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Scottish, Non restricted licence, without small vehicles, with medium vehicles, without novelty vehicles
            [
                [
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 0,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-medium-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15e',
                                                'value' => 'Confirmed'
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Scottish, Non restricted licence, without small vehicles, with large + medium vehicles, without novelty
            // vehicles
            [
                [
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-medium-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15e',
                                                'value' => 'Confirmed'
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Scottish, Restricted licence, without small vehicles, with large + medium vehicles, without novelty
            // vehicles
            [
                [
                    'psvMediumVhlConfirmation' => 'Y',
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvMediumVhlNotes' => "Foo\nBar",
                    'psvLimousines' => 'N',
                    'psvOperateSmallVhl' => 'Y',
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 2,
                    'totAuthLargeVehicles' => 2,
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_RESTRICTED
                    ],
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'title' => 'application-review-vehicles-declarations-medium-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15e',
                                                'value' => 'Confirmed'
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-business-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-8b1',
                                                'value' => 'Confirmed'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-vehicles-declarations-8b2',
                                                'noEscape' => true,
                                                'value' => "Foo<br />\nBar"
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-vehicles-declarations-novelty-title',
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        '15f' => [
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f1',
                                                'value' => 'No'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-declarations-15f2',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
