<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Delete an IRHP Candidate Permit
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Delete extends AbstractDeleteCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';
}
