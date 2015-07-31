<?php

/**
 * Update Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Service;

use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;

/**
 * Update Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentreHelperTest extends MockeryTestCase
{
    /**
     * @var UpdateOperatingCentreHelper
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new UpdateOperatingCentreHelper();
    }

    public function testAddMessages()
    {
        $this->sut->addMessage('foo', 'bar');
        $this->sut->addMessage('foo', 'cake', 'baz');

        $expected = [
            'foo' => [
                ['bar' => 'bar'],
                ['cake' => 'baz']
            ]
        ];

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    /**
     * @dataProvider validateTotalAuthVehiclesProvider
     */
    public function testValdiateTotalAuthVehicles($isRestricted, $data, $totals, $expected)
    {
        $entity = m::mock();
        $entity->shouldReceive('isRestricted')->andReturn($isRestricted);

        $this->sut->validateTotalAuthVehicles($entity, UpdateOperatingCentres::create($data), $totals);

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    /**
     * @dataProvider validateTotalAuthTrailersProvider
     */
    public function testValdiateTotalAuthTrailers($data, $totals, $expected)
    {
        $this->sut->validateTotalAuthTrailers(UpdateOperatingCentres::create($data), $totals);

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    /**
     * @dataProvider validatePsvProvider
     */
    public function testValidatePsv($canHaveLargeVehicles, $data, $expected)
    {
        $entity = m::mock();
        $entity->shouldReceive('canHaveLargeVehicles')->andReturn($canHaveLargeVehicles);

        $this->sut->validatePsv($entity, UpdateOperatingCentres::create($data));

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    public function validateTotalAuthVehiclesProvider()
    {
        return [
            'No OCs' => [
                false,
                [],
                [
                    'noOfOperatingCentres' => 0
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_V_4' => 'ERR_OC_V_4'],
                    ]
                ]
            ],
            'Restricted, too many vehicles, No OCs' => [
                true,
                [
                    'totAuthVehicles' => 3
                ],
                [
                    'noOfOperatingCentres' => 0
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_R_1' => 'ERR_OC_R_1'],
                        ['ERR_OC_V_4' => 'ERR_OC_V_4'],
                    ]
                ]
            ],
            '1 OC, less than required vehicle auth' => [
                false,
                [
                    'totAuthVehicles' => 10
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minVehicleAuth' => 11
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_V_1' => 'ERR_OC_V_1'],
                    ]
                ]
            ],
            '1 OC, more than required vehicle auth' => [
                false,
                [
                    'totAuthVehicles' => 12
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minVehicleAuth' => 11
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_V_1' => 'ERR_OC_V_1'],
                    ]
                ]
            ],
            '1 OC - Valid' => [
                false,
                [
                    'totAuthVehicles' => 11
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minVehicleAuth' => 11
                ],
                []
            ],
            'multiple OC, less than required' => [
                false,
                [
                    'totAuthVehicles' => 10
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minVehicleAuth' => 15,
                    'maxVehicleAuth' => 20
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_V_2' => 'ERR_OC_V_2'],
                    ]
                ]
            ],
            'multiple OC, more than required' => [
                false,
                [
                    'totAuthVehicles' => 25
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minVehicleAuth' => 15,
                    'maxVehicleAuth' => 20
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_V_3' => 'ERR_OC_V_3'],
                    ]
                ]
            ],
            'multiple OC valid' => [
                false,
                [
                    'totAuthVehicles' => 17
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minVehicleAuth' => 15,
                    'maxVehicleAuth' => 20
                ],
                []
            ]
        ];
    }

    public function validateTotalAuthTrailersProvider()
    {
        return [
            'No OCs' => [
                [],
                [
                    'noOfOperatingCentres' => 0
                ],
                [
                    'totAuthTrailers' => [
                        ['ERR_OC_T_4' => 'ERR_OC_T_4'],
                    ]
                ]
            ],
            '1 OC, less than required vehicle auth' => [
                [
                    'totAuthTrailers' => 10
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minTrailerAuth' => 11
                ],
                [
                    'totAuthTrailers' => [
                        ['ERR_OC_T_1' => 'ERR_OC_T_1'],
                    ]
                ]
            ],
            '1 OC, more than required vehicle auth' => [
                [
                    'totAuthTrailers' => 12
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minTrailerAuth' => 11
                ],
                [
                    'totAuthTrailers' => [
                        ['ERR_OC_T_1' => 'ERR_OC_T_1'],
                    ]
                ]
            ],
            '1 OC - Valid' => [
                [
                    'totAuthTrailers' => 11
                ],
                [
                    'noOfOperatingCentres' => 1,
                    'minTrailerAuth' => 11
                ],
                []
            ],
            'multiple OC, less than required' => [
                [
                    'totAuthTrailers' => 10
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minTrailerAuth' => 15,
                    'maxTrailerAuth' => 20
                ],
                [
                    'totAuthTrailers' => [
                        ['ERR_OC_T_2' => 'ERR_OC_T_2'],
                    ]
                ]
            ],
            'multiple OC, more than required' => [
                [
                    'totAuthTrailers' => 25
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minTrailerAuth' => 15,
                    'maxTrailerAuth' => 20
                ],
                [
                    'totAuthTrailers' => [
                        ['ERR_OC_T_3' => 'ERR_OC_T_3'],
                    ]
                ]
            ],
            'multiple OC valid' => [
                [
                    'totAuthTrailers' => 17
                ],
                [
                    'noOfOperatingCentres' => 5,
                    'minTrailerAuth' => 15,
                    'maxTrailerAuth' => 20
                ],
                []
            ]
        ];
    }

    public function validatePsvProvider()
    {
        return [
            'Valid Sum' => [
                false,
                [
                    'totAuthSmallVehicles' => 3,
                    'totAuthMediumVehicles' => 3,
                    'totAuthLargeVehicles' => 3,
                    'totAuthVehicles' => 9,
                ],
                []
            ],
            'Can have large, incorrect sum' => [
                true,
                [
                    'totAuthSmallVehicles' => 3,
                    'totAuthMediumVehicles' => 3,
                    'totAuthLargeVehicles' => 3,
                    'totAuthVehicles' => 15,
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_PSV_SUM_1A' => 'ERR_OC_PSV_SUM_1A']
                    ]
                ]
            ],
            'Cant have large, incorrect sum' => [
                false,
                [
                    'totAuthSmallVehicles' => 3,
                    'totAuthMediumVehicles' => 3,
                    'totAuthLargeVehicles' => 3,
                    'totAuthVehicles' => 15,
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_PSV_SUM_1B' => 'ERR_OC_PSV_SUM_1B']
                    ]
                ]
            ]
        ];
    }
}
