<?php

/**
 * Application Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationVehiclesPsvReviewService;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;

/**
 * Application Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationVehiclesPsvReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expectedMainItems, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $mockVehiclesPsv = m::mock();
        $this->sm->setService('Review\VehiclesPsv', $mockVehiclesPsv);
        $mockVehiclesPsv->shouldReceive('getConfigFromData')
            ->with($data, $expectedMainItems)
            ->andReturn('MAINITEMS');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    'hasEnteredReg' => 'N'
                ],
                [
                    [
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-hasEnteredReg',
                                    'value' => 'No'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => 'MAINITEMS'
                        ]
                    ]
                ]
            ],
            [
                [
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_SMALL
                                ],
                                'vrm' => 'SM10QWE',
                                'makeModel' => 'Foo Bar',
                                'isNovelty' => 'Y'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_SMALL
                                ],
                                'vrm' => 'SM11QWE',
                                'makeModel' => 'Foo Bar',
                                'isNovelty' => 'N'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_MEDIUM
                                ],
                                'vrm' => 'ME10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_MEDIUM
                                ],
                                'vrm' => 'ME11QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_LARGE
                                ],
                                'vrm' => 'LG10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => Vehicle::PSV_TYPE_LARGE
                                ],
                                'vrm' => 'LG11QWE'
                            ]
                        ]
                    ]
                ],
                [
                    [
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-hasEnteredReg',
                                    'value' => 'Yes'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => 'MAINITEMS'
                        ]
                    ]
                ]
            ]
        ];
    }
}
