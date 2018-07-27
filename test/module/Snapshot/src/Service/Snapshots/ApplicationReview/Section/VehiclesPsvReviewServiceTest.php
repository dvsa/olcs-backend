<?php

/**
 * Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VehiclesPsvReviewService;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;

/**
 * Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new VehiclesPsvReviewService($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($mainItems, $data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data, $mainItems));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'isVariation' => false,
                    'hasEnteredReg' => 'N'
                ],
                [
                    ['foo' => 'bar']
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'isVariation' => false,
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
                        [
                            'vehicle' => [
                                'vrm' => 'SM10QWE',
                                'makeModel' => 'Foo Bar',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'SM11QWE',
                                'makeModel' => 'Foo Bar',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'ME10QWE',
                                'makeModel' => '',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'ME11QWE',
                                'makeModel' => '',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'LG10QWE',
                                'makeModel' => '',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'LG11QWE',
                                'makeModel' => '',
                            ]
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'ME10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => ''
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'ME11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => ''
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => ''
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => ''
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'isVariation' => false,
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
                        [
                            'vehicle' => [
                                'vrm' => 'SM10QWE',
                                'makeModel' => 'Foo Bar',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'SM11QWE',
                                'makeModel' => 'Foo Bar',
                            ]
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'isVariation' => false,
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
                        [
                            'vehicle' => [
                                'vrm' => 'LG10QWE',
                                'makeModel' => 'ABC',
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'LG11QWE',
                                'makeModel' => 'XYZ',
                            ]
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'ABC'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'XYZ'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
