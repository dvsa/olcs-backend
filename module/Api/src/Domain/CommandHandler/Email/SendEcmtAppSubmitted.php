<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\EcmtAnnualPermitEmailTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send ECMT app submitted email
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtAppSubmitted extends AbstractEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use EcmtAnnualPermitEmailTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $template = 'ecmt-app-submitted';
    protected $subject = 'email.ecmt.default.subject';
    protected $extraRepos = ['FeeType'];
}
