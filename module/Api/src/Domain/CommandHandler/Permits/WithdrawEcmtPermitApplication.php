<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAutomaticallyWithdrawn;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractWithdrawApplicationHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;

/**
 * Withdraw an ECMT Permit application
 *
 * @author Scott Callaway
 */
final class WithdrawEcmtPermitApplication extends AbstractWithdrawApplicationHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $emails = [WithdrawableInterface::WITHDRAWN_REASON_UNPAID => SendEcmtAutomaticallyWithdrawn::class];
}
