<?php

/**
 * Update Operating Centre Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

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

    protected $authService;

    public function setUp(): void
    {
        $this->sut = new UpdateOperatingCentreHelper();

        $this->authService = m::mock();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->andReturn($this->authService);

        $this->sut->createService($sm);
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
    public function testValidatePsv($canHaveLargeVehicles, $isRestricted, $data, $expected)
    {
        $entity = m::mock();
        $entity->shouldReceive('isRestricted')->andReturn($isRestricted);

        $this->sut->validatePsv($entity, UpdateOperatingCentres::create($data));

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    public function testValidateEnforcementAreaValid()
    {
        $data = [
            'enforcementArea' => null
        ];

        $this->authService->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER)
            ->andReturn(true);

        $entity = m::mock();
        $entity->shouldReceive('getTrafficArea')
            ->andReturn(null);

        $command = UpdateOperatingCentres::create($data);

        $this->sut->validateEnforcementArea($entity, $command);

        $this->assertEquals([], $this->sut->getMessages());
    }

    public function testValidateEnforcementArea()
    {
        $data = [
            'enforcementArea' => null
        ];

        $this->authService->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER)
            ->andReturn(true);

        $entity = m::mock();
        $entity->shouldReceive('getTrafficArea')->andReturn('anything');

        $command = UpdateOperatingCentres::create($data);

        $this->sut->validateEnforcementArea($entity, $command);

        $messages = ['enforcementArea' => [['ERR_OC_EA_EMPTY' => 'ERR_OC_EA_EMPTY']]];

        $this->assertEquals($messages, $this->sut->getMessages());
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
                false,
                [
                    'totAuthVehicles' => 9,
                ],
                []
            ],
            'Restricted too many' => [
                true,
                true,
                [
                    'totAuthVehicles' => 9,
                ],
                [
                    'totAuthVehicles' => [
                        ['ERR_OC_R_1' => 'ERR_OC_R_1']
                    ]
                ]
            ],
        ];
    }
}
