<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\MarkSuccessfulDaPermitApplications as MarkSuccessfulDaPermitApplicationsCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Set successful permit applications for each devolved administration
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulDaPermitApplications extends AbstractCommandHandler implements TransactionedInterface
{
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
        $stockId = $command->getStockId();
        $candidatePermitIds = array();
        $daQuotas = $this->getRepo('IrhpPermitJurisdictionQuota')->fetchByNonZeroQuota($stockId);

        $result = new Result();
        $result->addMessage('STEP 2c:');
        $result->addMessage('  DAs associated with stock where quota > 0: ' . count($daQuotas));

        $candidatePermitIds = [];
        foreach ($daQuotas as $daQuota) {
            $daSuccessCount = $this->getRepo()->getSuccessfulDaCount(
                $stockId,
                $daQuota['jurisdictionId']
            );

            $daRemainingQuota = $daQuota['quotaNumber'] - $daSuccessCount;

            $result->addMessage('    DA with id ' . $daQuota['jurisdictionId'] . ':');
            $result->addMessage('      Derived values:');
            $result->addMessage('      - #DAQuota:          ' . $daQuota['quotaNumber']);
            $result->addMessage('      - #DASuccessCount:   ' . $daSuccessCount);
            $result->addMessage('      - #DARemainingQuota: ' . $daRemainingQuota);

            if ($daRemainingQuota > 0) {
                $daCandidatePermitIds = $this->getRepo()->getUnsuccessfulScoreOrderedUnderConsiderationIds(
                    $stockId,
                    $daQuota['jurisdictionId']
                );
                $truncatedDaCandidatePermitIds = array_slice($daCandidatePermitIds, 0, $daRemainingQuota);

                $candidatePermitIds = array_merge(
                    $candidatePermitIds,
                    $truncatedDaCandidatePermitIds
                );

                $result->addMessage('      Permits requesting this DA: ' . count($daCandidatePermitIds));
                $result->addMessage('      - adjusted for quota: ' . count($truncatedDaCandidatePermitIds));
                $result->addMessage('      ' . count($truncatedDaCandidatePermitIds) . ' permits will be marked as successful');
            } else {
                $result->addMessage('      #DARemainingQuota < 1 - nothing to do');
            }
        }

        $this->getRepo()->markAsSuccessful($candidatePermitIds);

        $result->addMessage('  ' . count($candidatePermitIds) . ' permits have been marked as successful');
        return $result;
    }
}
