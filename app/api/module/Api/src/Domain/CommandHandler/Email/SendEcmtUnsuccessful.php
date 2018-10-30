<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send confirmation unsuccessful ECMT application
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtUnsuccessful extends AbstractEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $template = 'ecmt-app-unsuccessful';
    protected $subject = 'email.ecmt.response.subject';
    protected $extraRepos = ['FeeType'];
}
