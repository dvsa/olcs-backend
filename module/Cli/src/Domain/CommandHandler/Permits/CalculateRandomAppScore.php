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
        try{
            $irhpCandidatePermits = $this->getRepo()->getIrhpCandidatePermitsForScoring($command->getStockId());

            $deviationData = IrhpCandidatePermit::getDeviationData($irhpCandidatePermits);
            foreach ($irhpCandidatePermits as $irhpCandidatePermit) {
                $randomFactor = $irhpCandidatePermit->calculateRandomFactor($deviationData);

                $irhpCandidatePermit->setRandomizedScore(abs($randomFactor * $irhpCandidatePermit->getApplicationScore()));
                $irhpCandidatePermit->setRandomFactor($randomFactor);
                $this->getRepo()->save($irhpCandidatePermit);
            }

            $result->addMessage('Candidate Permit Records updated with their randomised scores.');
        } catch(Exception $e) {
            $result->addMessage('ERROR');
        }

        return $result;
    }
}
