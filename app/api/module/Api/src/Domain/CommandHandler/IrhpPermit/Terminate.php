<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\ExpireEcmtPermitApplication;
use Dvsa\Olcs\Transfer\Query\Permits\ValidEcmtPermits;

/**
 * Terminate Permit
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Terminate extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * Handle terminate permit command
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $permit = $this->getRepo()->fetchById($command->getId());
        $terminatedStatus = $this->refData(IrhpPermit::STATUS_TERMINATED);

        try {
            $permit->proceedToStatus($terminatedStatus);
        } catch (ForbiddenException $exception) {
            $this->result->addMessage('The permit is not in the correct state to be terminated.');
            return $this->result;
        }

        $this->getRepo()->save($permit);

        $this->result->addId('IrhpPermit', $permit->getId());
        $this->result->addMessage('The selected permit has been terminated.');

        $applicationId = $permit->getIrhpPermitApplication()->getEcmtPermitApplication()->getId();

        if ($this->checkIfLastPermit($applicationId)) {
            $this->result->merge(
                $this->handleSideEffect(
                    ExpireEcmtPermitApplication::create(
                        [
                            'id' => $applicationId
                        ]
                    )
                )
            );
        }
        return $this->result;
    }

    /**
     * Check if the applications has valid permits left
     *
     *
     * @param int $applicationId
     * @return bool
     */
    protected function checkIfLastPermit($applicationId)
    {
        $permitsTotal = $this->handleQuery(
            ValidEcmtPermits::create(
                [
                    'page' => 1,
                    'limit' => 10,
                    'id' => $applicationId
                ]
            )
        );

        if ($permitsTotal['count'] === 0) {
            return true;
        }
        return false;
    }
}
