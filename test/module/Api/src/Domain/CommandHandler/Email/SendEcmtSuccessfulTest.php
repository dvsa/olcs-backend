<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful as SendEcmtSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtSuccessful as SendEcmtSuccessfulHandler;

/**
 * Test the permit app successful email
 */
class SendEcmtSuccessfulTest extends AbstractPermitTest
{
    protected $command = SendEcmtSuccessfulCmd::class;
    protected $commandHandler = SendEcmtSuccessfulHandler::class;
    protected $template = 'ecmt-app-successful';
    protected $subject = 'email.ecmt.response.subject';
    protected $extraRepos = ['FeeType'];
}
