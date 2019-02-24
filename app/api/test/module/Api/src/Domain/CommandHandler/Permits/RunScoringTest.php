<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\RunScoring;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Cli\Domain\Command\Permits\InitialiseScope;
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

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL,
            IrhpPermitStockEntity::STATUS_SCORING_IN_PROGRESS,
            IrhpPermitStockEntity::STATUS_SCORING_SUCCESSFUL,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $stockId = 47;
        $deviation = 1.5;

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
        $command->shouldReceive('getDeviation')
            ->andReturn($deviation);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitStock->shouldReceive('statusAllowsRunScoring')
            ->andReturn(true);

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

        $irhpPermitStock->shouldReceive('proceedToScoringInProgress')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_SCORING_IN_PROGRESS])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $initialiseScopeResult = new Result();
        $initialiseScopeResult->addMessage('InitialiseScope output');
        $this->expectedSideEffect(
            InitialiseScope::class,
            ['stockId' => $stockId, 'deviation' => $deviation],
            $initialiseScopeResult
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

        $logContent = "InitialiseScope output\r\n".
            "MarkSuccessfulSector output\r\n" .
            "MarkSuccessfulDa output\r\n" .
            "MarkSuccessfulRemaining output\r\n" .
            "ApplyRanges output\r\n" .
            "UploadScoringResult output\r\n" .
            "Scoring process completed successfully.";

        $uploadScoringLogResult = new Result();
        $uploadScoringLogResult->addMessage('UploadScoringLog output');
        $this->expectedSideEffect(
            UploadScoringLog::class,
            ['logContent' => $logContent],
            $uploadScoringLogResult
        );

        $irhpPermitStock->shouldReceive('proceedToScoringSuccessful')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_SCORING_SUCCESSFUL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'InitialiseScope output',
                'MarkSuccessfulSector output',
                'MarkSuccessfulDa output',
                'MarkSuccessfulRemaining output',
                'ApplyRanges output',
                'UploadScoringResult output',
                'Scoring process completed successfully.',
                'UploadScoringLog output'
            ],
            $result->getMessages()
        );
    }

    public function testIncorrectStatus()
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getStatusDescription')
            ->andReturn('Stock accept pending');

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->ordered()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered();

        $irhpPermitStock->shouldReceive('statusAllowsRunScoring')
            ->andReturn(false);

        $irhpPermitStock->shouldReceive('proceedToScoringPrerequisiteFail')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Run scoring is not permitted when stock status is \'Stock accept pending\''],
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
            ->ordered()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered();

        $irhpPermitStock->shouldReceive('statusAllowsRunScoring')
            ->andReturn(true);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(function ($command) use ($stockId) {
                $this->assertInstanceOf(CheckRunScoringPrerequisites::class, $command);
                $this->assertEquals($stockId, $command->getId());

                return [
                    'result' => false,
                    'message' => 'Failure message from CheckRunScoringPrerequisites'
                ];
            });

        $irhpPermitStock->shouldReceive('proceedToScoringPrerequisiteFail')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_SCORING_PREREQUISITE_FAIL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Failure message from CheckRunScoringPrerequisites'],
            $result->getMessages()
        );
    }
}
