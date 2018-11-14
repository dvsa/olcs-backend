<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\RunScoring as RunScoringCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ResetScoring;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulDaPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulSectorPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulRemainingPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ApplyRangesToSuccessfulPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\CalculateRandomAppScore;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Exception;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringLog;

/**
 * Run scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RunScoring extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $repoServiceName = 'IrhpPermitStock';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    /** @var int */
    private $stockId;

    /**
     * Handle command
     *
     * @param RunScoringCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->stockId = $command->getId();

        $stockRepo = $this->getRepo();
        $stock = $stockRepo->fetchById($this->stockId);
        $stockRepo->refresh($stock);

        if ($stock->statusAllowsRunScoring()) {
            $prerequisiteResult = $this->handleQuery(
                CheckRunScoringPrerequisites::create(['id' => $this->stockId])
            );
        } else {
            $prerequisiteResult = [
                'result' => false,
                'message' => sprintf(
                    'Run scoring is not permitted with when stock status is \'%s\'',
                    $stock->getStatusDescription()
                )
            ];
        }

        if (!$prerequisiteResult['result']) {
            $stock->proceedToScoringPrerequisiteFail($this->refData(IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL));
            $stockRepo->save($stock);

            $this->result->addMessage('Prerequisite failed: ' . $prerequisiteResult['message']);
            return $this->result;
        }

        $stockIdParams = ['stockId' => $this->stockId];

        $stock->proceedToScoringInProgress($this->refData(IrhpPermitStock::STATUS_SCORING_IN_PROGRESS));
        $stockRepo->save($stock);

        try {
            $this->result->merge(
                $this->handleSideEffects(
                    [
                        ResetScoring::create($stockIdParams),
                        CalculateRandomAppScore::create($stockIdParams),
                        MarkSuccessfulSectorPermitApplications::create($stockIdParams),
                        MarkSuccessfulDaPermitApplications::create($stockIdParams),
                        MarkSuccessfulRemainingPermitApplications::create($stockIdParams),
                        ApplyRangesToSuccessfulPermitApplications::create($stockIdParams),
                    ]
                )
            );

            $stock->proceedToScoringSuccessful($this->refData(IrhpPermitStock::STATUS_SCORING_SUCCESSFUL));
            $stockRepo->save($stock);

            $dto = GetScoredPermitList::create($stockIdParams);
            $scoringResults = $this->handleQuery($dto);

            $this->result->merge(
                $this->handleSideEffects([
                    UploadScoringResult::create([
                        'csvContent' => $scoringResults['result'],
                        'fileDescription' => 'Scoring Results'
                    ]),
                ])
            );

            $this->result->addMessage('Scoring process completed successfully.');
        } catch (Exception $e) {
            $stock->proceedToScoringUnexpectedFail($this->refData(IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL));
            $stockRepo->save($stock);

            $this->result->addMessage('Scoring process failed: ' . $e->getMessage());
            return $this->handleReturn();
        }

        return $this->handleReturn();
    }

    /**
     * Handles return common pre-requisites,
     * mainly uploading a copy of the log
     *
     * @return Result
     */
    private function handleReturn()
    {
        // Upload copy of log output to the document store.
        // We want to do this regardless of whether the process fell-over or not
        $logOutput = implode("\r\n", $this->result->getMessages());

        $this->result->merge(
            $this->handleSideEffects([
                UploadScoringLog::create(['logContent' => $logOutput])
            ])
        );

        return $this->result;
    }
}
