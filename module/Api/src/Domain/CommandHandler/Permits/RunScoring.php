<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\RunScoring as RunScoringCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ResetScoring;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulDaPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulSectorPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulRemainingPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ApplyRangesToSuccessfulPermitApplications;
use Dvsa\Olcs\Cli\Domain\Command\Permits\CalculateRandomAppScore;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Exception;

/**
 * Run scoring
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class RunScoring extends AbstractStockCheckingCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitRange', 'IrhpPermit', 'IrhpCandidatePermit', 'IrhpPermitStock'];

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
        $stockIdParams = ['stockId' => $this->stockId];

        if (!$this->passesStockAvailabilityPrerequisite($this->stockId)) {
            $this->result->addMessage('Prerequisite failed: no permits remaining within this stock.');
            $this->updateStockStatus(IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL);
            return $this->result;
        }

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
            return $this->result;
        }

        // TODO: result->getMessages() contains the output that we want to write to the execution report
        // run the commands here to generate the execution and scoring reports

        return $this->result;
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
}
