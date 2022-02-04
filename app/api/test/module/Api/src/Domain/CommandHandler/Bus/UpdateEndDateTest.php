<?php

declare (strict_types = 1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\UpdateEndDate;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Transfer\Command\Bus\UpdateEndDate as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @see UpdateEndDate
 */
class UpdateEndDateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateEndDate();
        $this->mockRepo('Bus', BusRepo::class);

        parent::setUp();
    }

    public function testHandleCommand(): void
    {
        $busRegId = 99;
        $endDate = '2022-12-25';

        $command = Cmd::Create(
            [
                'id' => $busRegId,
                'endDate' => $endDate,
            ]
        );

        $busReg = m::mock(BusRegEntity::class);
        $busReg->expects('updateEndDate')->with($endDate);

        $this->repoMap['Bus']->expects('fetchById')->with($busRegId)->andReturn($busReg);
        $this->repoMap['Bus']->expects('save')->with($busReg);

        $expectedResult = [
            'id' => [
                'BusReg' => $busRegId,
            ],
            'messages' => [
                0 => UpdateEndDate::MSG_SUCCESS
            ],
        ];

        $this->assertEquals($expectedResult, $this->sut->handleCommand($command)->toArray());
    }
}
