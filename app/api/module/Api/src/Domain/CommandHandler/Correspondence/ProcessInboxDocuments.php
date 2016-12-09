<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Correspondence;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Olcs\Logging\Log\Logger;

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

    const MSG_SEND_REMINDER_EMAIL = 'Sending email reminder for licence %s to %s';
    const ERR_SEND_REMINDER = 'Error: Email reminder sending error for licence %s and org %s';

    protected $repoServiceName = 'CorrespondenceInbox';

    /**
     * Handle Command
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\Correspondence\ProcessInboxDocuments $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->sendReminders());
        $result->merge($this->printDocuments());

        return $result;
    }

    /**
     * Send Reminders
     *
     * @return Result
     */
    protected function sendReminders()
    {
        /** @var Repository\CorrespondenceInbox $repo */
        $repo = $this->getRepo();

        $result = new Result();
        $minDate         = $this->getDate('P1M');
        $maxReminderDate = $this->getDate('P2D');
        $emailList = $repo->getAllRequiringReminder($minDate, $maxReminderDate);
        $result->addMessage('Found ' . count($emailList) . ' records to email');

        Logger::debug('#REMOVE-ME Found ' . count($emailList) . ' records to email;');

        foreach ($emailList as $idx => $row) {
            /** @var \Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox $row */
            $document = $row->getDocument();
            Logger::debug(
                '#REMOVE-ME' . $idx . ' ProcessInboxDocs::sendReminders: ' .
                'type: ' . gettype($row) . '; ' .
                'class: ' . get_class($row) . '; ' .
                '$row->getDocument() === null: ' . var_export($document === null, 1) . ';'
            );

            $licence = $row->getLicence();
            $licId = $licence->getId();

            $continuationsDetails = $document->getContinuationDetails();
            $isContinuation = (
                count($continuationsDetails) > 0
                && $continuationsDetails[0]->getChecklistDocument() !== null
            );
            $emailType = $isContinuation ? self::EMAIL_TYPE_CONTINUATION : self::EMAIL_TYPE_STANDARD;

            $org = $licence->getOrganisation();

            // edge case; we expect to find email addresses otherwise we wouldn't
            // have created the CI record in the first place, but still something
            // we need to handle...
            if ($org->getAdminOrganisationUsers()->isEmpty()) {
                $result->addMessage('No admin email addresses for licence ' . $licId);
                continue;
            }

            $sentTo = [];
            try {
                foreach ($org->getAdminOrganisationUsers() as $orgUser) {
                    /** @var \Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser $orgUser */
                    $user = $orgUser->getUser();
                    $to = $user->getContactDetails()->getEmailAddress();

                    $message = new \Dvsa\Olcs\Email\Data\Message(
                        $to,
                        'email.licensing-information.' . $emailType . '.subject'
                    );
                    $message->setTranslateToWelsh($user->getTranslateToWelsh());

                    $this->sendEmailTemplate(
                        $message,
                        'email-inbox-reminder-' . $emailType,
                        [
                            'licNo' => $licence->getLicNo(),
                            // @NOTE the http://selfserve part gets replaced
                            'url' => 'http://selfserve/correspondence'
                        ]
                    );

                    $sentTo[] = $to;
                }
            } catch (\Exception $e) {
                $msg = sprintf(self::ERR_SEND_REMINDER, $licId, $org->getId());
                $result->addMessage($msg);

                Logger::err(
                    $msg,
                    [
                        'trace' => $e->getTrace(),
                    ]
                );
            }

            if (count($sentTo) > 0) {
                $result->addMessage(sprintf(self::MSG_SEND_REMINDER_EMAIL, $licId, implode($sentTo, ',')));

                $row->setEmailReminderSent('Y');
                $repo->save($row);
            }
        }

        return $result;
    }

    /**
     * Print Documents
     *
     * @return Result
     */
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

            $result->addMessage('Printing document for licence ' . $licence->getId());

            $printQueue = EnqueueFileCommand::create(
                [
                    'documentId' => $document->getId(),
                    'jobName' => $document->getDescription()
                ]
            );

            $this->result->merge($this->handleSideEffect($printQueue));

            $row->setPrinted('Y');
            $this->getRepo()->save($row);
        }

        return $result;
    }

    /**
     * Get Date
     *
     * @param int $interval Interval
     *
     * @return \DateTime
     */
    protected function getDate($interval)
    {
        $now = new DateTime('now');
        return $now->sub(new \DateInterval($interval));
    }
}
