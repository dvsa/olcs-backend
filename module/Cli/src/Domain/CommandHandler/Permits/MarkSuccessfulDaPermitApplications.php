<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\MarkSuccessfulDaPermitApplications as MarkSuccessfulDaPermitApplicationsCommand;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Set successful permit applications for each devolved administration
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulDaPermitApplications extends ScoringCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpCandidatePermit';

    protected $extraRepos = ['IrhpPermitJurisdictionQuota'];

    /**
     * Handle command
     *
     * @param CommandInterface|MarkSuccessfulDaPermitApplicationsCommand $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->profileMessage('mark successful da permit applications...');

        $stockId = $command->getStockId();
        $candidatePermitIds = array();
        $daQuotas = $this->getRepo('IrhpPermitJurisdictionQuota')->fetchByNonZeroQuota($stockId);

        $this->result->addMessage('STEP 2c:');
        $this->result->addMessage('  DAs associated with stock where quota > 0: ' . count($daQuotas));

        $candidatePermitIds = [];
        foreach ($daQuotas as $daQuota) {
            $daSuccessCount = $this->getRepo()->getSuccessfulDaCountInScope(
                $stockId,
                $daQuota['jurisdictionId']
            );

            $daRemainingQuota = $daQuota['quotaNumber'] - $daSuccessCount;

            $this->result->addMessage('    DA with id ' . $daQuota['jurisdictionId'] . ':');
            $this->result->addMessage('      Derived values:');
            $this->result->addMessage('      - #DAQuota:          ' . $daQuota['quotaNumber']);
            $this->result->addMessage('      - #DASuccessCount:   ' . $daSuccessCount);
            $this->result->addMessage('      - #DARemainingQuota: ' . $daRemainingQuota);

            if ($daRemainingQuota > 0) {
                $daCandidatePermitIds = $this->getRepo()->getUnsuccessfulScoreOrderedIdsInScope(
                    $stockId,
                    $daQuota['jurisdictionId']
                );
                $truncatedDaCandidatePermitIds = array_slice($daCandidatePermitIds, 0, $daRemainingQuota);

                $candidatePermitIds = array_merge(
                    $candidatePermitIds,
                    $truncatedDaCandidatePermitIds
                );

                $this->result->addMessage('      Permits requesting this DA: ' . count($daCandidatePermitIds));
                $this->result->addMessage('      - adjusted for quota: ' . count($truncatedDaCandidatePermitIds));
                $this->result->addMessage('      The following ' . count($truncatedDaCandidatePermitIds) . ' permits will be marked as successful:');
                foreach ($truncatedDaCandidatePermitIds as $candidatePermitId) {
                    $this->result->addMessage('        - id = ' . $candidatePermitId);
                }
            } else {
                $this->result->addMessage('      #DARemainingQuota < 1 - nothing to do');
            }
        }

        $this->getRepo()->markAsSuccessful($candidatePermitIds);

        $this->result->addMessage('  ' . count($candidatePermitIds) . ' permits have been marked as successful');
        return $this->result;
    }
}
