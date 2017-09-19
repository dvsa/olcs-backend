<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention\UpdateActionConfirmation;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\DataRetention\UpdateActionConfirmation as Command;
use Mockery as m;

/**
 * Class UpdateActionConfirmationTest
 *
 * @covers UpdateActionConfirmation
 */
class UpdateActionConfirmationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateActionConfirmation();
        $this->mockRepo('DataRetention', Repository\DataRetention::class);

        parent::setUp();
    }

    public function testActionConfirmationTrueSetToFalse()
    {
        $command = Command::create(['ids' => [100]]);

        // Record with actionConfirmation set to 0
        // Should become a record with actionConfirmation set to 1
        $dataRetentionRecordCurrent = m::mock(DataRetention::class);
        $dataRetentionRecordCurrent->shouldReceive('getActionConfirmation')
            ->once()
            ->andReturn(true)
            ->shouldReceive('getNextReviewDate')
            ->once()
            ->andReturn(false)
            ->shouldReceive('setActionConfirmation')
            ->once()
            ->with(false)
            ->shouldReceive('setActionedDate')
            ->once();

        // Should become a record with actionConfirmation set to 1
        $this->repoMap['DataRetention']
            ->shouldReceive('fetchById')
            ->with(100)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('save')
            ->once()
            ->with($dataRetentionRecordCurrent);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Data retention record(s) updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testActionConfirmationFalseSetToTrue()
    {
        $command = Command::create(['ids' => [100]]);

        // Record with actionConfirmation set to 0
        // Should become a record with actionConfirmation set to 1
        $dataRetentionRecordCurrent = m::mock(DataRetention::class);
        $dataRetentionRecordCurrent->shouldReceive('getActionConfirmation')
            ->once()
            ->andReturn(false)
            ->shouldReceive('getNextReviewDate')
            ->once()
            ->andReturn(false)
            ->shouldReceive('setActionConfirmation')
            ->once()
            ->with(true)
            ->shouldReceive('setActionedDate')
            ->once();

        // Should become a record with actionConfirmation set to 1
        $this->repoMap['DataRetention']
            ->shouldReceive('fetchById')
            ->with(100)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('save')
            ->once()
            ->with($dataRetentionRecordCurrent);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Data retention record(s) updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testActionConfirmationFalseButCannotSetToTrue()
    {
        $command = Command::create(['ids' => [100]]);

        // Record with actionConfirmation set to 0
        // Should become a record with actionConfirmation set to 1
        $dataRetentionRecordCurrent = m::mock(DataRetention::class);
        $dataRetentionRecordCurrent->shouldReceive('getActionConfirmation')
            ->twice()
            ->andReturn(false)
            ->shouldReceive('getNextReviewDate')
            ->once()
            ->andReturn(true);

        // Should become a record with actionConfirmation set to 1
        $this->repoMap['DataRetention']
            ->shouldReceive('fetchById')
            ->with(100)
            ->once()
            ->andReturn($dataRetentionRecordCurrent);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Data retention record(s) updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testActionConfirmationTrueButCannotDeleteSoResetToFalse()
    {
        $command = Command::create(['ids' => [100]]);

        // Record with actionConfirmation set to 0
        // Should become a record with actionConfirmation set to 1
        $dataRetentionRecordCurrent = m::mock(DataRetention::class);
        $dataRetentionRecordCurrent->shouldReceive('getActionConfirmation')
            ->twice()
            ->andReturn(true)
            ->shouldReceive('getNextReviewDate')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setActionConfirmation')
            ->once()
            ->with(false)
            ->shouldReceive('setActionedDate')
            ->once();

        // Should become a record with actionConfirmation set to 1
        $this->repoMap['DataRetention']
            ->shouldReceive('fetchById')
            ->with(100)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('save')
            ->once()
            ->with($dataRetentionRecordCurrent);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Data retention record(s) updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testMultipleActionConfirmationTrueSetToFalse()
    {
        $command = Command::create(['ids' => [100, 200, 300]]);

        // Record with actionConfirmation set to 0
        // Should become a record with actionConfirmation set to 1
        $dataRetentionRecordCurrent = m::mock(DataRetention::class);
        $dataRetentionRecordCurrent->shouldReceive('getActionConfirmation')
            ->times(3)
            ->andReturn(true)
            ->shouldReceive('getNextReviewDate')
            ->times(3)
            ->andReturn(false)
            ->shouldReceive('setActionConfirmation')
            ->times(3)
            ->with(false)
            ->shouldReceive('setActionedDate')
            ->times(3);

        // Should become a record with actionConfirmation set to 1
        $this->repoMap['DataRetention']
            ->shouldReceive('fetchById')
            ->with(100)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('fetchById')
            ->with(200)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('fetchById')
            ->with(300)
            ->once()
            ->andReturn($dataRetentionRecordCurrent)
            ->shouldReceive('save')
            ->times(3)
            ->with($dataRetentionRecordCurrent);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '3 Data retention record(s) updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
