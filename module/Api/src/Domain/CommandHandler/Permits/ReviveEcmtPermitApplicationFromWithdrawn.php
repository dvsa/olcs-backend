<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateDefinedValue;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Revive ECMT Permit Application from withdrawn state
 */
final class ReviveEcmtPermitApplicationFromWithdrawn extends AbstractUpdateDefinedValue implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityMethodName = 'reviveFromWithdrawn';
    protected $definedValue = IrhpInterface::STATUS_UNDER_CONSIDERATION;
    protected $isRefData = true;
}
