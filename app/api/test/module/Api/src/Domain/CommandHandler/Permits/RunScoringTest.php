<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\RunScoring;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ResetScoring;
use Dvsa\Olcs\Cli\Domain\Command\Permits\CalculateRandomAppScore;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulSectorPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulDaPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulRemainingPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ApplyRangesToSuccessfulPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringLog;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class RunScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);

        $this->sut = m::mock(RunScoring::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $stockId = 47;

        $csvContent = [
            ['column1', 'column2'],
            ['row1value1', 'row1value2'],
            ['row2value1', 'row2value2'],
        ];

        $scoringResults = [
            'result' => $csvContent
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getStatus->getId')
            ->once()
            ->andReturn(IrhpPermitStockEntity::STATUS_SCORING_PENDING);

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($command) use ($stockId, $scoringResults) {
                if ($command instanceof CheckRunScoringPrerequisites) {
                    $this->assertEquals($stockId, $command->getId());

                    return [
                        'result' => true,
                        'message' => 'Prerequisites ok'
                    ];
                } elseif ($command instanceof GetScoredPermitList) {
                    $this->assertEquals($stockId, $command->getStockId());

                    return $scoringResults;
                } else {
                    throw new RuntimeException('Unknown class passed into handleQuery');
                }
            });

        $this->repoMap['IrhpPermitStock']->shouldReceive('updateStatus')
            ->with($stockId, IrhpPermitStockEntity::STATUS_SCORING_IN_PROGRESS)
            ->once()
            ->ordered();

        $resetScoringResult = new Result();
        $resetScoringResult->addMessage('ResetScoring output');
        $this->expectedSideEffect(
            ResetScoring::class,
            ['stockId' => $stockId],
            $resetScoringResult
        );

        $calculateRandomResult = new Result();
        $calculateRandomResult->addMessage('CalculateRandom output');
        $this->expectedSideEffect(
            CalculateRandomAppScore::class,
            ['stockId' => $stockId],
            $calculateRandomResult
        );

        $markSuccessfulSectorResult = new Result();
        $markSuccessfulSectorResult->addMessage('MarkSuccessfulSector output');
        $this->expectedSideEffect(
            MarkSuccessfulSectorPermitApplications::class,
            ['stockId' => $stockId],
            $markSuccessfulSectorResult
        );

        $markSuccessfulDaResult = new Result();
        $markSuccessfulDaResult->addMessage('MarkSuccessfulDa output');
        $this->expectedSideEffect(
            MarkSuccessfulDaPermitApplications::class,
            ['stockId' => $stockId],
            $markSuccessfulDaResult
        );

        $markSuccessfulRemainingResult = new Result();
        $markSuccessfulRemainingResult->addMessage('MarkSuccessfulRemaining output');
        $this->expectedSideEffect(
            MarkSuccessfulRemainingPermitApplications::class,
            ['stockId' => $stockId],
            $markSuccessfulRemainingResult
        );

        $applyRangesResult = new Result();
        $applyRangesResult->addMessage('ApplyRanges output');
        $this->expectedSideEffect(
            ApplyRangesToSuccessfulPermitApplications::class,
            ['stockId' => $stockId],
            $applyRangesResult
        );

        $uploadScoringResultResult = new Result();
        $uploadScoringResultResult->addMessage('UploadScoringResult output');
        $this->expectedSideEffect(
            UploadScoringResult::class,
            ['csvContent' => $csvContent],
            $uploadScoringResultResult
        );

        $logContent = "ResetScoring output\r\n" .
            "CalculateRandom output\r\n".
            "MarkSuccessfulSector output\r\n" .
            "MarkSuccessfulDa output\r\n" .
            "MarkSuccessfulRemaining output\r\n" .
            "ApplyRanges output\r\n" .
            "Scoring process completed successfully.\r\n" .
            "UploadScoringResult output";

        $uploadScoringLogResult = new Result();
        $uploadScoringLogResult->addMessage('UploadScoringLog output');
        $this->expectedSideEffect(
            UploadScoringLog::class,
            ['logContent' => $logContent],
            $uploadScoringLogResult
        );

        $this->repoMap['IrhpPermitStock']->shouldReceive('updateStatus')
            ->with($stockId, IrhpPermitStockEntity::STATUS_SCORING_SUCCESSFUL)
            ->once()
            ->ordered();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'ResetScoring output',
                'CalculateRandom output',
                'MarkSuccessfulSector output',
                'MarkSuccessfulDa output',
                'MarkSuccessfulRemaining output',
                'ApplyRanges output',
                'Scoring process completed successfully.',
                'UploadScoringResult output',
                'UploadScoringLog output'
            ],
            $result->getMessages()
        );
    }

    /**
     * @dataProvider invalidStatusIdsProvider
     */
    public function testIncorrectStatus($stockStatusId)
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getStatus->getId')
            ->once()
            ->andReturn($stockStatusId);

        $this->repoMap['IrhpPermitStock']->shouldReceive('updateStatus')
            ->with($stockId, IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Stock status must be stock_scoring_pending, currently '.$stockStatusId],
            $result->getMessages()
        );
    }

    public function testPrerequisiteFailed()
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getStatus->getId')
            ->once()
            ->andReturn(IrhpPermitStockEntity::STATUS_SCORING_PENDING);

        $this->sut->shouldReceive('handleQuery')
            ->andReturnUsing(function ($command) use ($stockId) {
                $this->assertInstanceOf(CheckRunScoringPrerequisites::class, $command);
                $this->assertEquals($stockId, $command->getId());

                return [
                    'result' => false,
                    'message' => 'Failure message from CheckRunScoringPrerequisites'
                ];
            });

        $this->repoMap['IrhpPermitStock']->shouldReceive('updateStatus')
            ->with($stockId, IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Failure message from CheckRunScoringPrerequisites'],
            $result->getMessages()
        );
    }

    public function invalidStatusIdsProvider()
    {
        return [
            [IrhpPermitStockEntity::STATUS_SCORING_NEVER_RUN],
            [IrhpPermitStockEntity::STATUS_SCORING_IN_PROGRESS],
            [IrhpPermitStockEntity::STATUS_SCORING_SUCCESSFUL],
            [IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL],
            [IrhpPermitStockEntity::STATUS_SCORING_UNEXPECTED_FAIL],
            [IrhpPermitStockEntity::STATUS_ACCEPT_PENDING],
            [IrhpPermitStockEntity::STATUS_ACCEPT_IN_PROGRESS],
            [IrhpPermitStockEntity::STATUS_ACCEPT_SUCCESSFUL],
            [IrhpPermitStockEntity::STATUS_ACCEPT_PREREQUISITE_FAIL],
            [IrhpPermitStockEntity::STATUS_ACCEPT_UNEXPECTED_FAIL],
        ];
    }
}
