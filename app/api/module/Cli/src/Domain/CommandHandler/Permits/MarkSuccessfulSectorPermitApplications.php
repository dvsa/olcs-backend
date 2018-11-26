<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\MarkSuccessfulSectorPermitApplications
    as MarkSuccessfulSectorPermitApplicationsCommand;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Set successful permit applications for each sector
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

class MarkSuccessfulSectorPermitApplications extends ScoringCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitSectorQuota';

    protected $extraRepos = ['IrhpCandidatePermit'];

    /**
     * Handle command
     *
     * @param CommandInterface|MarkSuccessfulSectorPermitApplicationsCommand $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->profileMessage('mark successful sector permit applications...');

        $sectorQuotas = $this->getRepo()->fetchByNonZeroQuota(
            $command->getStockId()
        );

        $this->result->addMessage('STEP 2b:');
        $this->result->addMessage('  Sectors associated with stock where quota > 0: ' . count($sectorQuotas));

        $candidatePermitIds = [];
        foreach ($sectorQuotas as $sectorQuota) {
            $sectorCandidatePermitIds = $this->getRepo('IrhpCandidatePermit')->getScoreOrderedIdsBySectorInScope(
                $command->getStockId(),
                $sectorQuota['sectorId']
            );
            $truncatedSectorCandidatePermitIds = array_slice($sectorCandidatePermitIds, 0, $sectorQuota['quotaNumber']);

            $this->result->addMessage('    Sector with id ' . $sectorQuota['sectorId'] . ':');
            $this->result->addMessage('      Derived values: ');
            $this->result->addMessage('      - #sectorQuota: ' . $sectorQuota['quotaNumber']);
            $this->result->addMessage('      Permits requesting this sector: ' . count($sectorCandidatePermitIds));
            $this->result->addMessage('      - adjusted for quota: ' . count($truncatedSectorCandidatePermitIds));
            $this->result->addMessage(
                '      The following ' . count($truncatedSectorCandidatePermitIds) . ' permits will be marked as successful:'
            );
            foreach ($truncatedSectorCandidatePermitIds as $candidatePermitId) {
                $this->result->addMessage('        - id = ' . $candidatePermitId);
            }

            $candidatePermitIds = array_merge($candidatePermitIds, $truncatedSectorCandidatePermitIds);
        }

        $this->getRepo('IrhpCandidatePermit')->markAsSuccessful($candidatePermitIds);

        $this->result->addMessage('  ' . count($candidatePermitIds) . ' permits have been marked as successful');
        return $this->result;
    }
}
