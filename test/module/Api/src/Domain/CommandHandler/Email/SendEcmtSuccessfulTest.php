<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful as SendEcmtSuccessfulCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtSuccessful as SendEcmtSuccessfulHandler;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

/**
 * Test the permit app successful email
 */
class SendEcmtSuccessfulTest extends AbstractEcmtAnnualPermitTest
{
    protected $commandClass = SendEcmtSuccessfulCmd::class;
    protected $commandHandlerClass = SendEcmtSuccessfulHandler::class;
    protected $template = 'ecmt-app-successful';
    protected $subject = 'email.ecmt.response.subject';
    protected $permitApplicationRepo = 'EcmtPermitApplication';
    protected $applicationEntityClass = EcmtPermitApplication::class;
}
