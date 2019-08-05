<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful as SendEcmtUnsuccessfulCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Email\SendEcmtUnsuccessful as SendEcmtUnsuccessfulHandler;

/**
 * Test the permit app unsuccessful email
 */
class SendEcmtUnsuccessfulTest extends AbstractEcmtAnnualPermitTest
{
    protected $commandClass = SendEcmtUnsuccessfulCmd::class;
    protected $commandHandlerClass = SendEcmtUnsuccessfulHandler::class;
    protected $template = 'ecmt-app-unsuccessful';
    protected $subject = 'email.ecmt.response.subject';
    protected $permitApplicationRepo = 'EcmtPermitApplication';
    protected $applicationEntityClass = EcmtPermitApplication::class;
}
