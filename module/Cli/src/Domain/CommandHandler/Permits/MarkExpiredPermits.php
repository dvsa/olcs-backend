<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkExpiredPermits as MarkExpiredPermitsCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Mark expired permits
 */
class MarkExpiredPermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpPermit';

    protected $extraRepos = ['IrhpApplication', 'EcmtPermitApplication'];

    /**
     * Handle command
     *
     * @param MarkExpiredPermitsCmd $command command
     *
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleCommand(CommandInterface $command)
    {
        // mark all permits with validity date in the past as expired
        $this->getRepo('IrhpPermit')->markAsExpired();

        // mark all IRHP applications without valid permits as expired
        $this->getRepo('IrhpApplication')->markAsExpired();

        // mark all ECMT applications without valid permits as expired
        $this->getRepo('EcmtPermitApplication')->markAsExpired();

        $this->result->addMessage('Expired permits have been marked');

        return $this->result;
    }
}
