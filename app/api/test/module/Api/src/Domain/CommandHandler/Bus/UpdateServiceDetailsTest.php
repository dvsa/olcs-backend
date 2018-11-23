<?php

/**
 * Update Service Details Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\UpdateServiceDetails;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Domain\Repository\BusNoticePeriod as BusNoticePeriodRepo;
use Dvsa\Olcs\Api\Domain\Repository\BusRegOtherService as BusRegOtherServiceRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Bus\UpdateServiceDetails as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CmdCreateBusFee;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Update Service DetailsTest
 */
class UpdateServiceDetailsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateServiceDetails();
        $this->mockRepo('Bus', BusRepo::class);
        $this->mockRepo('BusNoticePeriod', BusNoticePeriodRepo::class);
        $this->mockRepo('BusRegOtherService', BusRegOtherServiceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            BusNoticePeriodEntity::class => [
                2 => m::mock(BusNoticePeriodEntity::class)
            ],
            BusServiceTypeEntity::class => [
                5 => m::mock(BusServiceTypeEntity::class)
            ]
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     *
     * @note         we don't test the two dates here relate to each other properly, that is tested elsewhere
     * First date is included for completeness
     *
     * @dataProvider testHandleCommandProvider
     *
     * @param bool  $createFee
     * @param array $expectedData
     */
    public function testHandleCommand($createFee, $expectedData)
    {
        $busRegId = 99;
        $serviceNumber = 12345;
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $via = 'via';
        $otherDetails = 'other details';
        $effectiveDate = '';
        $receivedDate = '';
        $endDate = '';
        $busNoticePeriod = 2;
        $otherServices = [
            [
                'id' => 19,
                'version' => 1,
                'serviceNo' => 0,
            ],
            [
                'id' => 21,
                'version' => 1,
                'serviceNo' => '1b',
            ],
            [
                'id' => 22,
                'version' => 1,
                'serviceNo' => null,
            ],
            [
                'id' => null,
                'version' => 1,
                'serviceNo' => 1,
            ],
        ];
        $busServiceTypes = [
            0 => 5
        ];

        $command = Cmd::Create(
            [
                'id' => $busRegId,
                'serviceNumber' => $serviceNumber,
                'startPoint' => $startPoint,
                'finishPoint' => $finishPoint,
                'via' => $via,
                'otherDetails' => $otherDetails,
                'receivedDate' => $receivedDate,
                'effectiveDate' => $effectiveDate,
                'endDate' => $endDate,
                'busNoticePeriod' => $busNoticePeriod,
                'otherServices' => $otherServices,
                'busServiceTypes' => $busServiceTypes
            ]
        );

        $mockOtherService = $this->repoMap['BusRegOtherService'];
        $mockedEntity = m::mock(BusRegOtherServiceEntity::class)->shouldReceive('setServiceNo')
            ->with(0)
            ->getMock();
        $mockedEntity->shouldReceive('getId')->andReturn(19);

        $mockedEntity2 = m::mock(BusRegOtherServiceEntity::class)->shouldReceive('setServiceNo')
            ->with('1b')
            ->getMock();
        $mockedEntity2->shouldReceive('getId')->andReturn(21);


        $mockOtherService->shouldReceive('fetchById')
            ->with(19, 1, 1)
            ->andReturn(
                $mockedEntity
            )->getMock();

        $mockOtherService->shouldReceive('fetchById')
            ->with(21, 1, 1)
            ->andReturn(
                $mockedEntity2
            )->getMock();
        $mockOtherService->shouldReceive('save')->with($mockedEntity)->getMock();
        $mockOtherService->shouldReceive('save')->with($mockedEntity2)->getMock();
        $mockOtherService->shouldReceive('save')->with(any(BusRegOtherService::class));

        /** @var BusRegOtherServiceEntity $busReg */
        $mockBusRegObjectOtherServiceEntity = m::mock(BusRegOtherServiceEntity::class);
        $mockBusRegObjectOtherServiceEntity->shouldReceive('getId')->andReturn(123);

        /** @var RefDataEntity $mockStatus */
        $mockStatus = m::mock(RefDataEntity::class);
        $mockStatus->shouldReceive('getId')->andReturn(BusRegEntity::STATUS_NEW);

        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class)->makePartial();
        $busReg->shouldReceive('updateServiceDetails')
            ->once()
            ->shouldReceive('getId')
            ->andReturn($busRegId)
            ->shouldReceive('getStatus')
            ->andReturn($mockStatus)
            ->shouldReceive('setBusServiceTypes')
            ->with(m::type(ArrayCollection::class))
            ->once()
            ->shouldReceive('getOtherServices')
            ->andReturn([0 => $mockBusRegObjectOtherServiceEntity])
            ->shouldReceive('shouldCreateFee')
            ->once()
            ->andReturn($createFee);

        $this->repoMap['Bus']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($busReg)
            ->shouldReceive('save')
            ->with(m::type(BusRegEntity::class))
            ->once();

        $mockBusNoticePeriodEntity = m::mock(BusNoticePeriodEntity::class);

        $this->repoMap['BusNoticePeriod']->shouldReceive('fetchById')
            ->andReturn($mockBusNoticePeriodEntity);

        if ($createFee) {
            $createFeeResult = new Result();
            $createFeeResult
                ->addId('fee', 999)
                ->addMessage('bus reg fee created');
            $this->expectedSideEffect(CmdCreateBusFee::class, ['id' => $busRegId], $createFeeResult);
        }

        $busReg = m::mock(BusReg::class);

        $busReg->shouldReceive('getOtherServices')->andReturn(
            [
                (new BusRegOtherService($busReg, 19))->setId(19),
                (new BusRegOtherService($busReg, 20))->setId(20)
            ]
        )->getMock();
        $mockOtherService->shouldReceive('delete')->times(1);

        $result = $this->sut->handleCommand($command);

        $this->assertSame($expectedData, $result->getIds());
    }

    /**
     * return array
     */
    public function testHandleCommandProvider()
    {
        return [
            [
                'createFee' => true,
                'expectedResult' => [
                    'BusRegOtherService' => [
                        19,
                        21,
                        null,
                    ],
                    'fee' => 999,
                    'BusReg' => 99,
                ]
            ],
            [
                'createFee' => false,
                'expectedResult' => [
                    'BusRegOtherService' => [
                        19,
                        21,
                        null,
                    ],
                    'BusReg' => 99,
                ]
            ]
        ];
    }
}
