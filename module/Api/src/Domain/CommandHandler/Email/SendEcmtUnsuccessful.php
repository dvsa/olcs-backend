<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\EcmtAnnualPermitEmailTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;

/**
 * Send confirmation unsuccessful ECMT application
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtUnsuccessful extends AbstractEmailHandler
{
    use EcmtAnnualPermitEmailTrait;
    use PermitEmailTrait;

    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-annual-apsg-app-unsuccessful';
    protected $subject = 'email.ecmt.response.subject';
    protected $extraRepos = ['FeeType'];
}
