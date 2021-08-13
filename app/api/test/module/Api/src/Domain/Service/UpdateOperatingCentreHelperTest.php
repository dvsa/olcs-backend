<?php

namespace Dvsa\OlcsTest\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use ZfcRbac\Service\AuthorizationService;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;
use Dvsa\OlcsTest\Api\Entity\Application\ApplicationBuilder;
use Dvsa\OlcsTest\Api\Entity\Licence\LicenceBuilder;

/**
 * @see UpdateOperatingCentreHelper
 */
class UpdateOperatingCentreHelperTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const TOTAL_AUTH_VEHICLES_COMMAND_PROPERTY = 'totAuthVehicles';

    /**
     * @var UpdateOperatingCentreHelper|null
     */
    protected $sut;

    public function testAddMessages()
    {
        $this->setUpSut();
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

    /**
     * @dataProvider validateTotalAuthTrailersProvider
     */
    public function testValdiateTotalAuthTrailers($data, $totals, $expected)
    {
        $this->setUpSut();
        $this->sut->validateTotalAuthTrailers(UpdateOperatingCentres::create($data), $totals);

        $this->assertEquals($expected, $this->sut->getMessages());
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

    /**
     * @dataProvider validatePsvProvider
     */
    public function testValidatePsv($canHaveLargeVehicles, $isRestricted, $data, $expected)
    {
        $this->setUpSut();
        $licenceBuilder = LicenceBuilder::aPsvLicence();
        if ($isRestricted) {
            $licenceBuilder->ofTypeRestricted();
        }
        $entity = ApplicationBuilder::applicationForLicence($licenceBuilder)->build();

        $this->sut->validatePsv($entity, UpdateOperatingCentres::create($data));

        $this->assertEquals($expected, $this->sut->getMessages());
    }

    public function testValidateEnforcementAreaValid()
    {
        $this->setUpSut();
        $data = [
            'enforcementArea' => null
        ];

        $this->authService()->shouldReceive('isGranted')
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
        $this->setUpSut();
        $data = [
            'enforcementArea' => null
        ];

        $this->authService()->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER)
            ->andReturn(true);

        $entity = m::mock();
        $entity->shouldReceive('getTrafficArea')->andReturn('anything');

        $command = UpdateOperatingCentres::create($data);

        $this->sut->validateEnforcementArea($entity, $command);

        $messages = ['enforcementArea' => [['ERR_OC_EA_EMPTY' => 'ERR_OC_EA_EMPTY']]];

        $this->assertEquals($messages, $this->sut->getMessages());
    }

    /**
     * @test
     */
    public function validateTotalAuthVehicles_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'validateTotalAuthVehicles']);
    }

    /**
     * @return array
     */
    public function invalidTotalAuthVehicleConfigurations(): array
    {
        return [
            'No OCs' => [
                /* Number of vehicles to be authorized on the application: */ null,
                /* Number of operating centres: */ 0,
                /* Operating centre vehicle constraints[min,max]: */ [0, 11],
                /* Expected validation error code: */ 'ERR_OC_V_4',
            ],
            '1 OC, less vehicles then the minimum vehicles supported' => [
                /* Number of vehicles to be authorized on the application: */ 10,
                /* Number of operating centres: */ 1,
                /* Operating centre vehicle constraints[min,max]: */ [11, 11],
                /* Expected validation error code: */ 'ERR_OC_V_1',
            ],
            '1 OC, more vehicles than the minimum vehicles supported' => [
                /* Number of vehicles to be authorized on the application: */ 12,
                /* Number of operating centres: */ 1,
                /* Operating centre vehicle constraints[min,max]: */ [11, 11],
                /* Expected validation error code: */ 'ERR_OC_V_1',
            ],
            '>1 OC, less vehicles then the minimum vehicles supported' => [
                /* Number of vehicles to be authorized on the application: */ 14,
                /* Number of operating centres: */ 2,
                /* Operating centre vehicle constraints[min,max]: */ [15, 20],
                /* Expected validation error code: */ 'ERR_OC_V_2',
            ],
            'multiple OC, more vehicles than the maximum vehicles supported' => [
                /* Number of vehicles to be authorized on the application: */ 21,
                /* Number of operating centres: */ 2,
                /* Operating centre vehicle constraints[min,max]: */ [15, 20],
                /* Expected validation error code: */ 'ERR_OC_V_3',
            ],
        ];
    }

    /**
     * @param ?int $totAuthVehicles
     * @param int $operatingCentreCount,
     * @param array $operatingCentreConstraints
     * @param string $validationErrorCode
     * @test
     * @dataProvider invalidTotalAuthVehicleConfigurations
     * @depends validateTotalAuthVehicles_IsCallable
     */
    public function validateTotalAuthVehicles_AddsValidationMessages_WhereTotalAuthVehiclesValueIsInvalid(
        ?int $totAuthVehicles,
        int $operatingCentreCount,
        array $operatingCentreConstraints,
        string $validationErrorCode
    ) {
        // Setup
        $this->setUpSut();
        $command = UpdateOperatingCentres::create(['totAuthVehicles' => $totAuthVehicles]);
        $operatingCentreConstraints = array_combine(['minVehicleAuth', 'maxVehicleAuth'], $operatingCentreConstraints);
        $operatingCentreConstraints['noOfOperatingCentres'] = $operatingCentreCount;
        $entity = ApplicationBuilder::application()->build();

        // Execute
        $this->sut->validateTotalAuthVehicles($entity, $command, $operatingCentreConstraints);

        // Assert
        $this->assertSame($validationErrorCode, $this->sut->getMessages()[static::TOTAL_AUTH_VEHICLES_COMMAND_PROPERTY][0][$validationErrorCode] ?? null);
    }

    /**
     * @return array
     */
    public function validTotalAuthVehicleConfigurations(): array
    {
        return [
            '1 OC' => [
                /* Number of vehicles to be authorized on the application: */ 11,
                /* Number of operating centres: */ 1,
                /* Operating centre vehicle constraints[min,max]: */ [11, 11],
            ],
            '>1 OC, lowest possible number of authorized vehicles' => [
                /* Number of vehicles to be authorized on the application: */ 15,
                /* Number of operating centres: */ 2,
                /* Operating centre vehicle constraints[min,max]: */ [15, 25],
            ],
            '>1 OC, highest possible number of authorized vehicles' => [
                /* Number of vehicles to be authorized on the application: */ 25,
                /* Number of operating centres: */ 2,
                /* Operating centre vehicle constraints[min,max]: */ [15, 25],
            ],
        ];
    }

    /**
     * @param ?int $totAuthVehicles
     * @param int $operatingCentreCount
     * @param array $operatingCentreConstraints
     * @test
     * @dataProvider validTotalAuthVehicleConfigurations
     * @depends validateTotalAuthVehicles_IsCallable
     */
    public function validateTotalAuthVehicles_DoesNotAddValidationMessages_WhereTotalAuthVehiclesValueIsValid(
        int $totAuthVehicles,
        int $operatingCentreCount,
        array $operatingCentreConstraints
    ) {
        // Setup
        $this->setUpSut();
        $command = UpdateOperatingCentres::create([static::TOTAL_AUTH_VEHICLES_COMMAND_PROPERTY => $totAuthVehicles]);
        $entity = ApplicationBuilder::application()->build();
        $operatingCentreConstraints = array_combine(['minVehicleAuth', 'maxVehicleAuth'], $operatingCentreConstraints);
        $operatingCentreConstraints['noOfOperatingCentres'] = $operatingCentreCount;

        // Execute
        $this->sut->validateTotalAuthVehicles($entity, $command, $operatingCentreConstraints);

        // Assert
        $this->assertSame([], $this->sut->getMessages());
    }

    /**
     * @test
     * @depends validateTotalAuthVehicles_DoesNotAddValidationMessages_WhereTotalAuthVehiclesValueIsValid
     */
    public function validateTotalAuthVehicles_AcceptsLicence()
    {
        // Setup
        $this->setUpSut();
        [$totAuthVehicles, $operatingCentreCount, $operatingCentreConstraints] = array_values($this->validTotalAuthVehicleConfigurations())[0];
        $command = UpdateOperatingCentres::create(['totAuthVehicles' => $totAuthVehicles]);
        $entity = LicenceBuilder::aLicence()->build();
        $operatingCentreConstraints = array_combine(['minVehicleAuth', 'maxVehicleAuth'], $operatingCentreConstraints);
        $operatingCentreConstraints['noOfOperatingCentres'] = $operatingCentreCount;

        // Execute
        $this->sut->validateTotalAuthVehicles($entity, $command, $operatingCentreConstraints);

        // Assert
        $this->assertTrue(true, 'Expected no exception to be thrown');
    }

    /**
     * @test
     */
    public function validatePsv_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'validatePsv']);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    public function setUpSut(): void
    {
        $this->sut = new UpdateOperatingCentreHelper();
        $this->sut->createService($this->serviceManager());
    }

    protected function setUpDefaultServices(): void
    {
        $this->authService();
    }

    /**
     * @return m\MockInterface|AuthorizationService
     */
    protected function authService(): m\MockInterface
    {
        if (! $this->serviceManager()->has(AuthorizationService::class)) {
            $instance = m::mock(AuthorizationService::class);
            $this->serviceManager()->setService(AuthorizationService::class, $instance);
        }
        return $this->serviceManager()->get(AuthorizationService::class);
    }
}
