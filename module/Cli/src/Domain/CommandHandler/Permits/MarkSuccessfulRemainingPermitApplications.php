<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
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
class MarkSuccessfulRemainingPermitApplications extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

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
        $stockId = $command->getStockId();

        $availableStockCount = $this->getRepo('IrhpPermitRange')->getCombinedRangeSize($stockId);
        $validPermitCount = $this->getRepo('IrhpPermit')->getPermitCount($stockId);
        $allocationQuota = $availableStockCount - $validPermitCount;

        $successfulPaCount = $this->getRepo()->getSuccessfulCount($stockId);
        $remainingQuota = $allocationQuota - $successfulPaCount;

        $result = new Result();
        $result->addMessage('STEP 2d:');
        $result->addMessage('  Derived values:');
        $result->addMessage('    - #availableStockCount: ' . $availableStockCount);
        $result->addMessage('    - #validPermitCount:    ' . $validPermitCount);
        $result->addMessage('    - #allocationQuota:     ' . $allocationQuota);
        $result->addMessage('    - #successfulPACount:   ' . $successfulPaCount);
        $result->addMessage('    - #remainingQuota:      ' . $remainingQuota);

        // TODO: could remainingQuota ever be zero or less?
        if ($remainingQuota > 0) {
            $candidatePermitIds = $this->getRepo()->getUnsuccessfulScoreOrderedIds($stockId);

            $truncatedCandidatePermitIds = array_slice($candidatePermitIds, 0, $remainingQuota);
            $this->getRepo()->markAsSuccessful($truncatedCandidatePermitIds);

            $result->addMessage('  Unsuccessful remaining permits found in stock: ' . count($candidatePermitIds));
            $result->addMessage('  Marking ' . count($truncatedCandidatePermitIds) . ' permits as successful');
        } else {
            $result->addMessage('#remainingQuota < 1 - nothing to do');
        }

        return $result;
    }
}
