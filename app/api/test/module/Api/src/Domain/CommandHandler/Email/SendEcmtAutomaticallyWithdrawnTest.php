<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAutomaticallyWithdrawn as SendEcmtAutomaticallyWithdrawnCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtAutomaticallyWithdrawn as SendEcmtAutomaticallyWithdrawnHandler;

/**
 * Test the permit app successful email
 */
class SendEcmtAutomaticallyWithdrawnTest extends AbstractPermitTest
{
    protected $command = SendEcmtAutomaticallyWithdrawnCmd::class;
    protected $commandHandler = SendEcmtAutomaticallyWithdrawnHandler::class;
    protected $template = 'ecmt-automatically-withdrawn';
    protected $subject = 'email.ecmt.automatically.withdrawn.subject';
    protected $extraRepos = ['FeeType'];
}
