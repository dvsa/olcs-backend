<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\GenerateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\RegenerateIssueFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateMultipleNoOfPermits;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\MaxStockPermitsByApplication;
use Mockery as m;
use RuntimeException;

class UpdateMultipleNoOfPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);

        $this->sut = m::mock(UpdateMultipleNoOfPermits::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
 
        parent::setUp();
    }

    public function testPermitCountChanged()
    {
        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn(17);
        $irhpApplication->shouldReceive('storePermitsRequired')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('resetCheckAnswersAndDeclaration')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('saveOnFlush')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2019->shouldReceive('updatePermitsRequired')
            ->with(2)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2019)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2020->shouldReceive('updatePermitsRequired')
            ->with(4)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2020)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationFr2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2019->shouldReceive('updatePermitsRequired')
            ->with(0)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2019)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('flushAll')
            ->once()
            ->ordered()
            ->globally();

        $irhpApplication->shouldReceive('hasPermitsRequiredChanged')
            ->once()
            ->ordered()
            ->globally()
            ->andReturn(true);

        $this->expectedSideEffect(
            GenerateApplicationFee::class,
            ['id' => $irhpApplicationId],
            new Result()
        );

        $this->expectedSideEffect(
            RegenerateIssueFee::class,
            ['id' => $irhpApplicationId],
            new Result()
        );

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($irhpApplicationId) {
                $this->assertInstanceOf(MaxStockPermitsByApplication::class, $query);
                $this->assertEquals($irhpApplicationId, $query->getId());

                return [
                    'result' => [
                        7 => 12,
                        8 => 4,
                        9 => 2
                    ]
                ];
            });

        $irhpApplicationWithStockInfoResponse = [
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2019,
                'validTo' => '2019-12-31',
                'countryId' => 'ES',
                'stockId' => 7
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2020,
                'validTo' => '2020-12-31',
                'countryId' => 'ES',
                'stockId' => 8
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2019,
                'validTo' => '2019-08-15',
                'countryId' => 'FR',
                'stockId' => 9
            ]
        ];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('getByIrhpApplicationWithStockInfo')
            ->andReturn($irhpApplicationWithStockInfoResponse);

        $commandPermitsRequired = [
            'ES' => [
                2019 => 2,
                2020 => 4
            ],
            'FR' => [
                2019 => 0
            ],
            'randominput' => 'bob',
            'junk' => [
                'bar',
                'bar2'
            ]
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $command->shouldReceive('getPermitsRequired')
            ->andReturn($commandPermitsRequired);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            [
                'Updated 3 required permit counts for IRHP application'
            ],
            $result->getMessages()
        );
    }

    public function testPermitCountNotChanged()
    {
        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn(17);
        $irhpApplication->shouldReceive('storePermitsRequired')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('resetCheckAnswersAndDeclaration')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('saveOnFlush')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2019->shouldReceive('updatePermitsRequired')
            ->with(2)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2019)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2020->shouldReceive('updatePermitsRequired')
            ->with(4)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2020)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationFr2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2019->shouldReceive('updatePermitsRequired')
            ->with(0)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2019)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('flushAll')
            ->once()
            ->ordered()
            ->globally();

        $irhpApplication->shouldReceive('hasPermitsRequiredChanged')
            ->once()
            ->ordered()
            ->globally()
            ->andReturn(false);

        $this->expectedSideEffect(
            GenerateApplicationFee::class,
            ['id' => $irhpApplicationId],
            new Result()
        );

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($irhpApplicationId) {
                $this->assertInstanceOf(MaxStockPermitsByApplication::class, $query);
                $this->assertEquals($irhpApplicationId, $query->getId());

                return [
                    'result' => [
                        7 => 12,
                        8 => 4,
                        9 => 2
                    ]
                ];
            });

        $irhpApplicationWithStockInfoResponse = [
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2019,
                'validTo' => '2019-12-31',
                'countryId' => 'ES',
                'stockId' => 7
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2020,
                'validTo' => '2020-12-31',
                'countryId' => 'ES',
                'stockId' => 8
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2019,
                'validTo' => '2019-08-15',
                'countryId' => 'FR',
                'stockId' => 9
            ]
        ];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('getByIrhpApplicationWithStockInfo')
            ->andReturn($irhpApplicationWithStockInfoResponse);

        $commandPermitsRequired = [
            'ES' => [
                2019 => 2,
                2020 => 4
            ],
            'FR' => [
                2019 => 0
            ],
            'randominput' => 'bob',
            'junk' => [
                'bar',
                'bar2'
            ]
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $command->shouldReceive('getPermitsRequired')
            ->andReturn($commandPermitsRequired);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            [
                'Updated 3 required permit counts for IRHP application'
            ],
            $result->getMessages()
        );
    }

    public function testPermitsRequiredOutOfRange()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Out of range data for country ES in year 2020 - expected range 0 to 4 but received 7'
        );

        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn(17);
        $irhpApplication->shouldReceive('storePermitsRequired')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('resetCheckAnswersAndDeclaration')
            ->never();
        $irhpApplication->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('saveOnFlush')
            ->with($irhpApplication)
            ->never();

        $irhpPermitApplicationEs2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2019->shouldReceive('updatePermitsRequired')
            ->with(2)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2019)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2020->shouldReceive('updatePermitsRequired')
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2020)
            ->never();

        $irhpPermitApplicationFr2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2019->shouldReceive('updatePermitsRequired')
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2019)
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('flushAll')
            ->never();

        $irhpApplication->shouldReceive('hasPermitsRequiredChanged')
            ->never();

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($irhpApplicationId) {
                $this->assertInstanceOf(MaxStockPermitsByApplication::class, $query);
                $this->assertEquals($irhpApplicationId, $query->getId());

                return [
                    'result' => [
                        7 => 12,
                        8 => 4,
                        9 => 2
                    ]
                ];
            });

        $irhpApplicationWithStockInfoResponse = [
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2019,
                'validTo' => '2019-12-31',
                'countryId' => 'ES',
                'stockId' => 7
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2020,
                'validTo' => '2020-12-31',
                'countryId' => 'ES',
                'stockId' => 8
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2019,
                'validTo' => '2019-08-15',
                'countryId' => 'FR',
                'stockId' => 9
            ]
        ];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('getByIrhpApplicationWithStockInfo')
            ->andReturn($irhpApplicationWithStockInfoResponse);

        $commandPermitsRequired = [
            'ES' => [
                2019 => 2,
                2020 => 7
            ],
            'FR' => [
                2019 => 0
            ],
            'randominput' => 'bob',
            'junk' => [
                'bar',
                'bar2'
            ]
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $command->shouldReceive('getPermitsRequired')
            ->andReturn($commandPermitsRequired);

        $this->sut->handleCommand($command);
    }

    public function testMissingPermitsRequiredData()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Missing data or incorrect type for country ES in year 2020'
        );

        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn(17);
        $irhpApplication->shouldReceive('storePermitsRequired')
            ->once()
            ->ordered()
            ->globally();
        $irhpApplication->shouldReceive('resetCheckAnswersAndDeclaration')
            ->never();
        $irhpApplication->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('saveOnFlush')
            ->with($irhpApplication)
            ->never();

        $irhpPermitApplicationEs2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2019->shouldReceive('updatePermitsRequired')
            ->with(2)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2019)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2020->shouldReceive('updatePermitsRequired')
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2020)
            ->never();

        $irhpPermitApplicationFr2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2019->shouldReceive('updatePermitsRequired')
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2019)
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('flushAll')
            ->never();

        $irhpApplication->shouldReceive('hasPermitsRequiredChanged')
            ->never();

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($irhpApplicationId) {
                $this->assertInstanceOf(MaxStockPermitsByApplication::class, $query);
                $this->assertEquals($irhpApplicationId, $query->getId());

                return [
                    'result' => [
                        7 => 12,
                        8 => 4,
                        9 => 2
                    ]
                ];
            });

        $irhpApplicationWithStockInfoResponse = [
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2019,
                'validTo' => '2019-12-31',
                'countryId' => 'ES',
                'stockId' => 7
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2020,
                'validTo' => '2020-12-31',
                'countryId' => 'ES',
                'stockId' => 8
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2019,
                'validTo' => '2019-08-15',
                'countryId' => 'FR',
                'stockId' => 9
            ]
        ];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('getByIrhpApplicationWithStockInfo')
            ->andReturn($irhpApplicationWithStockInfoResponse);

        $commandPermitsRequired = [
            'ES' => [
                2019 => 2
            ],
            'FR' => [
                2019 => 0
            ],
            'randominput' => 'bob',
            'junk' => [
                'bar',
                'bar2'
            ]
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $command->shouldReceive('getPermitsRequired')
            ->andReturn($commandPermitsRequired);

        $this->sut->handleCommand($command);
    }

    public function testIsNotReadyForNoOfPermits()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('IRHP application is not ready for number of permits');

        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(false);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->sut->handleCommand($command);
    }
}
