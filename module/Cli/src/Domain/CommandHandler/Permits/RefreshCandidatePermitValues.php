<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Refresh Candidate Permit Values
 */
final class RefreshCandidatePermitValues extends AbstractCommandHandler implements ToggleRequiredInterface
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
        $candidatePermitRepo = $this->getRepo();

        $candidatePermits = $candidatePermitRepo->getInStock($command->getStockId());
        $this->result->addMessage(count($candidatePermits). ' candidate permits to be updated');

        foreach ($candidatePermits as $candidatePermit) {
            $candidatePermit->refreshApplicationScoreAndIntensityOfUse();
            $candidatePermitRepo->saveOnFlush($candidatePermit);
        }

        $candidatePermitRepo->flushAll();
        $this->result->addMessage('Candidate permits updated');

        return $this->result;
    }
}
