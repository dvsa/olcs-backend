<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateDefinedValue;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;

/**
 * Update Permit status
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class Terminate extends AbstractUpdateDefinedValue implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';
    protected $entityMethodName = 'proceedToStatus';
    protected $definedValue = IrhpPermit::STATUS_TERMINATED;
    protected $isRefData = true;
}
