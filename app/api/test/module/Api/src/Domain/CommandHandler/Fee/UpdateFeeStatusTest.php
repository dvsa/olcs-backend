<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Fee\UpdateFeeStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\UpdateFeeStatus;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Fee Status Test
 */
class UpdateFeeStatusTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateFeeStatus();
        $this->mockRepo('Fee', Repository\FeeType::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create([
            'id' => 1,
            'status' => Fee::STATUS_REFUNDED
        ]);

        $mockFee = m::mock(Fee::class);
        $mockFee->shouldReceive('setFeeStatus')->once();
        $mockFee->shouldReceive('getId')->once()->andReturn(1);

        $this->repoMap['Fee']
            ->shouldReceive('fetchById')
            ->once()
            ->with($command->getId())
            ->andReturn($mockFee);

        $this->repoMap['Fee']->shouldReceive('save')->with($mockFee);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 1
            ],
            'messages' => [
                'Fee status updated:lfs_refunded'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
