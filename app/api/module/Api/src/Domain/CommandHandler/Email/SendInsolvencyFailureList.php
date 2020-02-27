<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class SendInsolvencyFailureList extends AbstractCommandHandler
{
    use EmailAwareTrait;

    /**
     * @inheritDoc
     */
    public function handleCommand(CommandInterface $command)
    {
        $organisationIds = $command->getOrganisationNumbers();
        $emailSubject = $command->getEmailSubject();
        $emailAddress = $command->getEmailAddress();

        $emailMessage = new Message($emailAddress, $emailSubject);
        $emailMessage->setPlainBody(implode("\n", $organisationIds));

        $this->result->merge($this->sendEmail($emailMessage));

        return $this->result;
    }
}
