<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Proceed to status
 */
final class ProceedToStatus extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $status = $this->refData($command->getStatus());

        $permits = $this->getRepo()->fetchByIds($command->getIds());

        foreach ($permits as $permit) {
            // the command is used as a side effect after rollback in GeneratePermits
            // refresh the entity from the database
            $this->getRepo()->refresh($permit);

            $permit->proceedToStatus($status);

            $this->getRepo()->save($permit);

            $this->result->addId($status->getId(), $permit->getId(), true);
        }

        $this->result->addMessage('Permits proceeded to '.$status->getDescription());

        return $this->result;
    }
}
