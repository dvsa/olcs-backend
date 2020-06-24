<?php

/**
 * Variation Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationVehiclesPsvReviewService;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;

/**
 * Variation Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new VariationVehiclesPsvReviewService();

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

        $mockVehiclesPsv = m::mock();
        $this->sm->setService('Review\VehiclesPsv', $mockVehiclesPsv);
        $mockVehiclesPsv->shouldReceive('getConfigFromData')
            ->with($data, [])
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
                                'vrm' => 'ME10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'ME11QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'LG10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'LG11QWE'
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
