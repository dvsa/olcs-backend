<?php

namespace Dvsa\Olcs\Api\Domain\Command\Email;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Send email to notify that EBSR submission has been refused by short notice
 */
final class SendEbsrRefusedBySn extends AbstractIdOnlyCommand
{

}
