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
        $result = new Result();
        $irhpCandidatePermits = $this->getRepo()->getIrhpCandidatePermitsForScoring($command->getStockId());
        $totalPermitCount = count($irhpCandidatePermits);
        $servicedPermitCount = 0;

        if ($totalPermitCount > 0) {
            $deviationData = IrhpCandidatePermit::getDeviationData($irhpCandidatePermits);
            foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
                $randomFactor = $irhpCandidatePermit->calculateRandomFactor($deviationData);

                if ($irhpCandidatePermit->getRandomizedScore() === null) {
                    $irhpCandidatePermit->setRandomizedScore(abs($randomFactor * $irhpCandidatePermit->getApplicationScore()));
                    $irhpCandidatePermit->setRandomFactor($randomFactor);
                    $this->getRepo()->save($irhpCandidatePermit);
                    $servicedPermitCount++;
                }
            }
        }

        $result->addMessage('Updated the Randomised Score of Appropriate Candidate Permits.');
        $result->addMessage('   - Candidate Permit Count: ' . $totalPermitCount);
        $result->addMessage('   - Number of Permits Updated: ' . $servicedPermitCount);

        return $result;
    }
}
