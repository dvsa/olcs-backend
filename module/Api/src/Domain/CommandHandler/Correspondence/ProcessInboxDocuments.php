<?php

/**
 * Process inbox documents
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Correspondence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Service\File\File;

/**
 * Process inbox documents
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class ProcessInboxDocuments extends AbstractCommandHandler implements EmailAwareInterface
{
    use EmailAwareTrait;

    const EMAIL_TYPE_STANDARD = 'standard';
    const EMAIL_TYPE_CONTINUATION = 'continuation';

    protected $repoServiceName = 'CorrespondenceInbox';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->sendReminders());
        $result->merge($this->printDocuments());

        return $result;
    }

    protected function sendReminders()
    {
        $result = new Result();
        $minDate         = $this->getDate('P1M');
        $maxReminderDate = $this->getDate('P2D');
        $emailList = $this->getRepo()->getAllRequiringReminder($minDate, $maxReminderDate);
        $result->addMessage('Found ' . count($emailList) . ' records to email');

        foreach ($emailList as $row) {
            $licence = $row->getLicence();
            $organisation = $licence->getOrganisation();
            $continuationsDetails = $row->getDocument()->getContinuationDetails();
            $isContinuation = count($row->getDocument()->getContinuationDetails()) &&
                $continuationsDetails[0]->getChecklistDocument();

            $users = $organisation->getAdminEmailAddresses();

            // edge case; we expect to find email addresses otherwise we wouldn't
            // have created the CI record in the first place, but still something
            // we need to handle...
            if (empty($users)) {
                $result->addMessage('No admin email addresses for licence ' . $licence->getId());
                continue;
            }
            $result->addMessage(
                'Sending email reminder for licence ' . $licence->getId() . ' to ' . implode($users, ',')
            );
            $emailType = $isContinuation ? self::EMAIL_TYPE_CONTINUATION : self::EMAIL_TYPE_STANDARD;

            $message = new Message(
                $users,
                'email.licensing-information.' . $emailType  . '.subject'
            );
            $message->setTranslateToWelsh(false);
            $this->sendEmailTemplate(
                $message,
                'email-inbox-reminder-' . $emailType,
                [
                    'licNo' => $licence->getLicNo(),
                    // @NOTE the http://selfserve part gets replaced
                    'url' => 'http://selfserve/correspondence'
                ]
            );

            $row->setEmailReminderSent('Y');
            $this->getRepo()->save($row);
        }

        return $result;
    }

    protected function printDocuments()
    {
        $result = new Result();
        $minDate         = $this->getDate('P1M');
        $maxPrintDate    = $this->getDate('P1W');
        $printList = $this->getRepo()->getAllRequiringPrint($minDate, $maxPrintDate);
        $result->addMessage('Found ' . count($printList) . ' records to print');

        foreach ($printList as $row) {
            $licence = $row->getLicence();
            $document = $row->getDocument();

            $file = new File();
            $file->fromData(
                ['identifier' => $document->getIdentifier()]
            );
            $result->addMessage('Printing document for licence ' . $licence->getId());

            $printQueue = EnqueueFileCommand::create(
                [
                    'fileIdentifier' => $file->getIdentifier(),
                    'jobName' => $document->getDescription()
                ]
            );

            $this->result->merge($this->handleSideEffect($printQueue));

            $row->setPrinted('Y');
            $this->getRepo()->save($row);
        }

        return $result;
    }

    protected function getDate($interval)
    {
        $now = new DateTime('now');
        return $now->sub(new \DateInterval($interval));
    }
}
