<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedHandler;

/**
 * Test the permit app submitted email
 */
class SendEcmtAppSubmittedTest extends AbstractPermitTest
{
    protected $command = SendEcmtAppSubmittedCmd::class;
    protected $commandHandler = SendEcmtAppSubmittedHandler::class;
    protected $template = 'ecmt-app-submitted';
    protected $subject = 'email.ecmt.default.subject';
    protected $extraRepos = ['FeeType'];
}
