<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send confirmation of ECMT permits being issued
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtIssued extends AbstractEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $template = 'ecmt-app-issued';
    protected $subject = 'email.ecmt.issued.subject';
    protected $extraRepos = ['FeeType'];
}
