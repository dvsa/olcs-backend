<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Delete an IRHP Permit Range
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class Delete extends AbstractDeleteCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitRange';
    protected $extraError = 'irhp-permit-range-cannot-delete-active-dependencies';
}
