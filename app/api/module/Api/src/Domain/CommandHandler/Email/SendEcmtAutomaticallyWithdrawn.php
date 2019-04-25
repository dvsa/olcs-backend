<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send confirmation of ECMT app being successful
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class SendEcmtAutomaticallyWithdrawn extends AbstractEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $template = 'ecmt-automatically-withdrawn';
    protected $subject = 'email.ecmt.automatically.withdrawn.subject';
    protected $extraRepos = ['FeeType'];
}
