<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\MarkSuccessfulRemainingPermitApplications
    as MarkSuccessfulRemainingPermitApplicationsCommand;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Set the remaining successful permit applications
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulRemainingPermitApplications extends ScoringCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpCandidatePermit';

    protected $extraRepos = ['IrhpPermit', 'IrhpPermitRange'];

    /**
     * Handle command
     *
     * @param CommandInterface|MarkSuccessfulRemainingPermitApplicationsCommand $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->profileMessage('mark successful remaining permit applications...');

        $stockId = $command->getStockId();

        $availableStockCount = $this->getRepo('IrhpPermitRange')->getCombinedRangeSize($stockId);
        $validPermitCount = $this->getRepo('IrhpPermit')->getPermitCount($stockId);
        $allocationQuota = $availableStockCount - $validPermitCount;

        $successfulPaCount = $this->getRepo()->getSuccessfulCountInScope($stockId);
        $remainingQuota = $allocationQuota - $successfulPaCount;

        $this->result->addMessage('STEP 2d:');
        $this->result->addMessage('  Derived values:');
        $this->result->addMessage('    - #availableStockCount: ' . $availableStockCount);
        $this->result->addMessage('    - #validPermitCount:    ' . $validPermitCount);
        $this->result->addMessage('    - #allocationQuota:     ' . $allocationQuota);
        $this->result->addMessage('    - #successfulPACount:   ' . $successfulPaCount);
        $this->result->addMessage('    - #remainingQuota:      ' . $remainingQuota);

        // TODO: could remainingQuota ever be zero or less?
        if ($remainingQuota > 0) {
            $candidatePermitIds = $this->getRepo()->getUnsuccessfulScoreOrderedIdsInScope($stockId);

            $truncatedCandidatePermitIds = array_slice($candidatePermitIds, 0, $remainingQuota);
            $this->getRepo()->markAsSuccessful($truncatedCandidatePermitIds);

            $this->result->addMessage('  Unsuccessful remaining permits found in stock: ' . count($candidatePermitIds));
            $this->result->addMessage('  Marking the following' . count($truncatedCandidatePermitIds) . ' permits as successful');
            foreach ($truncatedCandidatePermitIds as $candidatePermitId) {
                $this->result->addMessage('    - id = ' . $candidatePermitId);
            }
        } else {
            $this->result->addMessage('#remainingQuota < 1 - nothing to do');
        }

        return $this->result;
    }
}
