<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful as SendEcmtPartSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtPartSuccessful as SendEcmtPartSuccessfulHandler;

/**
 * Test the permit app part successful email
 */
class SendEcmtPartSuccessfulTest extends AbstractPermitTest
{
    protected $command = SendEcmtPartSuccessfulCmd::class;
    protected $commandHandler = SendEcmtPartSuccessfulHandler::class;
    protected $template = 'ecmt-app-part-successful';
    protected $subject = 'email.ecmt.response.subject';
    protected $extraRepos = ['FeeType'];
}
