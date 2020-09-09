<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

/**
 * Test the permit app submitted email
 */
class SendEcmtAppSubmittedTest extends AbstractEcmtAnnualPermitTest
{
    protected $commandClass = SendEcmtAppSubmittedCmd::class;
    protected $commandHandlerClass = SendEcmtAppSubmittedHandler::class;
    protected $template = 'ecmt-annual-apsg-app-submitted';
    protected $subject = 'email.ecmt.default.subject';
    protected $permitApplicationRepo = 'IrhpApplication';
    protected $applicationEntityClass = IrhpApplication::class;
}
