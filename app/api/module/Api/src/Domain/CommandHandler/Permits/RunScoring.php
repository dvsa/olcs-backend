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

        $stock = $this->getRepo()->fetchById($this->stockId);
        $statusId = $stock->getStatus()->getId();

        if ($statusId == IrhpPermitStock::STATUS_SCORING_PENDING) {
            $prerequisiteResult = $this->handleQuery(
                CheckRunScoringPrerequisites::create(['id' => $this->stockId])
            );
        } else {
            $prerequisiteResult = [
                'result' => false,
                'message' => sprintf(
                    'Stock status must be %s, currently %s',
                    IrhpPermitStock::STATUS_SCORING_PENDING,
                    $statusId
                )
            ];
        }

        if (!$prerequisiteResult['result']) {
            $this->result->addMessage('Prerequisite failed: ' . $prerequisiteResult['message']);
            $this->updateStockStatus(IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL);
            return $this->result;
        }

        $stockIdParams = ['stockId' => $this->stockId];

        try {
            $this->updateStockStatus(IrhpPermitStock::STATUS_SCORING_IN_PROGRESS);

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

            $this->updateStockStatus(IrhpPermitStock::STATUS_SCORING_SUCCESSFUL);
            $this->result->addMessage('Scoring process completed successfully.');
        } catch (Exception $e) {
            $this->updateStockStatus(IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL);
            $this->result->addMessage('Scoring process failed: ' . $e->getMessage());
            return $this->handleReturn();
        }

        try {
            // Get data for scoring results
            $dto = GetScoredPermitList::create($stockIdParams);
            $scoringResults = $this->handleQuery($dto);

            // Upload scoring results file
            $this->result->merge(
                $this->handleSideEffects([
                    UploadScoringResult::create([
                        'csvContent' => $scoringResults['result'],
                        'fileDescription' => 'Scoring Results'
                    ]),
                ])
            );
        } catch (Exception $e) {
            $this->result->addMessage('Failed to upload scoring results: ' . $e->getMessage());
        }

        return $this->handleReturn();
    }

    /**
     * Update the status of the stock item
     *
     * @param string $status
     */
    private function updateStockStatus($status)
    {
        $this->getRepo('IrhpPermitStock')->updateStatus($this->stockId, $status);
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
