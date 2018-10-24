<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Calculate Random Application Score
 *
 */
final class CalculateRandomAppScore extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpCandidatePermit';

    /**
    * Handle command
    *
    * @param CommandInterface $command command
    *
    * @return Result
    */
    public function handleCommand(CommandInterface $command)
    {
        $stockId = $command->getStockId();

        if ($this->getRepo()->getCountWithRandomisedScore($stockId) > 0) {
            $this->result->addMessage('Stock has one or more randomised scores already assigned.');
            $this->result->addMessage('    - No randomised scores will be set.');

            return $this->result;
        }

        $irhpCandidatePermits = $this->getRepo()->getIrhpCandidatePermitsForScoring($stockId);
        $totalPermitCount = count($irhpCandidatePermits);

        if ($totalPermitCount > 0) {
            $deviationData = IrhpCandidatePermit::getDeviationData($irhpCandidatePermits);
            foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
                $randomFactor = $irhpCandidatePermit->calculateRandomFactor($deviationData);

                $irhpCandidatePermit->setRandomizedScore(abs($randomFactor * $irhpCandidatePermit->getApplicationScore()));
                $irhpCandidatePermit->setRandomFactor($randomFactor);
                $this->getRepo()->save($irhpCandidatePermit);
            }
        }

        $this->result->addMessage('Updated the Randomised Score of Appropriate Candidate Permits.');
        $this->result->addMessage('   - Number of Permits Updated: ' . $totalPermitCount);

        return $this->result;
    }
}
