<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
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

class MarkSuccessfulSectorPermitApplications extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
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
        $sectorQuotas = $this->getRepo()->fetchByNonZeroQuota(
            $command->getStockId()
        );

        $result = new Result();
        $result->addMessage('STEP 2b:');
        $result->addMessage('  Sectors associated with stock where quota > 0: ' . count($sectorQuotas));

        $candidatePermitIds = [];
        foreach ($sectorQuotas as $sectorQuota) {
            $sectorCandidatePermitIds = $this->getRepo('IrhpCandidatePermit')->getScoreOrderedIdsBySector(
                $command->getStockId(),
                $sectorQuota['sectorId']
            );
            $truncatedSectorCandidatePermitIds = array_slice($sectorCandidatePermitIds, 0, $sectorQuota['quotaNumber']);

            $result->addMessage('    Sector with id ' . $sectorQuota['sectorId'] . ':');
            $result->addMessage('      Derived values: ');
            $result->addMessage('      - #sectorQuota: ' . $sectorQuota['quotaNumber']);
            $result->addMessage('      Permits requesting this sector: ' . count($sectorCandidatePermitIds));
            $result->addMessage('      - adjusted for quota: ' . count($truncatedSectorCandidatePermitIds));
            $result->addMessage(
                '      ' . count($truncatedSectorCandidatePermitIds) . ' permits will be marked as successful'
            );

            $candidatePermitIds = array_merge($candidatePermitIds, $truncatedSectorCandidatePermitIds);
        }

        $this->getRepo('IrhpCandidatePermit')->markAsSuccessful($candidatePermitIds);

        $result->addMessage('  ' . count($candidatePermitIds) . ' permits have been marked as successful');
        return $result;
    }
}
