<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

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
final class CalculateRandomApplicationScore extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermitApplication';

    protected $extraRepos = ['IrhpCandidatePermit'];


    /**
    * Handle command
    *
    * @param CommandInterface $command command
    *
    * @return Result
    */
    public function handleCommand(CommandInterface $command)
    {
        $irhpCandidatePermits = $this->getRepo('IrhpCandidatePermit')->getIrhpCandidatePermitsForScoring($command->getId());

        $deviationData = IrhpCandidatePermit::getDeviationData($irhpCandidatePermits);

        foreach ($irhpCandidatePermits as $irhpCandidatePermit) {

            $randomisedScore = $irhpCandidatePermit->calculateRandomisedScore($deviationData);

            //$irhpCandidatePermit->setRandomizedScore($randomisedScore);
            $irhpCandidatePermit->setRandomizedScore($randomisedScore * $irhpCandidatePermit->getApplicationScore());
            $this->getRepo('IrhpCandidatePermit')->save($irhpCandidatePermit);
        }

        $result = new Result();
        $result->addMessage('Candidate Permit Records updated with their randomised scores.');

        return $result;
    }
}
