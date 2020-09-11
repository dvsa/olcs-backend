<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful as SendEcmtPartSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtPartSuccessful as SendEcmtPartSuccessfulHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

/**
 * Test the permit app part successful email
 */
class SendEcmtPartSuccessfulTest extends AbstractEcmtAnnualPermitTest
{
    protected $commandClass = SendEcmtPartSuccessfulCmd::class;
    protected $commandHandlerClass = SendEcmtPartSuccessfulHandler::class;
    protected $template = 'ecmt-annual-apsg-app-part-successful';
    protected $subject = 'email.ecmt.response.subject';
    protected $permitApplicationRepo = 'IrhpApplication';
    protected $applicationEntityClass = IrhpApplication::class;
}
