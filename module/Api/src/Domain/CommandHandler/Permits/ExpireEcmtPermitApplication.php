<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateDefinedValue;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

/**
 * Change ECMT Permit Application status to expired
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class ExpireEcmtPermitApplication extends AbstractUpdateDefinedValue implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityMethodName = 'expire';
    protected $definedValue = EcmtPermitApplication::STATUS_EXPIRED;
    protected $isRefData = true;
}
