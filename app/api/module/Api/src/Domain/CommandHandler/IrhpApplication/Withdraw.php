<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermAutomaticallyWithdrawn;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermUnsuccessful;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractWithdrawApplicationHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;

/**
 * Withdraw an IRHP Permit application
 *
 * @author Andy Newton <ian@hemera-business-services.co.uk>
 */
final class Withdraw extends AbstractWithdrawApplicationHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';
}
