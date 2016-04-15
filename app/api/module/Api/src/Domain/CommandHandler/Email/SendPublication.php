<?php

/**
 * Send Publication document via email
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepository;
use Dvsa\Olcs\Api\Domain\Command\Email\SendPublication as SendPublicationEmailCmd;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Send Publication document via email
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class SendPublication extends AbstractCommandHandler implements EmailAwareInterface
{
    use EmailAwareTrait;

    protected $repoServiceName = 'Publication';

    protected $template = null;

    /**
     * @todo we need the proper email address - email sent to John Spellman for clarification 06/04/2016
     */
    const TO_EMAIL = 'terry.valtech+publication@gmail.com';
    const EMAIL_TEMPLATE = 'publication-published';
    const EMAIL_SUBJECT = 'email.send-publication';
    const EMAIL_POLICE_SUBJECT = 'email.send-publication-police';

    /**
     * @param CommandInterface|SendPublicationEmailCmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var $repo PublicationRepository $repo
         * @var PublicationEntity $publication
         */
        $repo = $this->getRepo();
        $publication = $repo->fetchUsingId($command);

        $trafficArea = $publication->getTrafficArea();

        //recipients
        $isPolice = $command->getIsPolice();
        $recipients = $trafficArea->getPublicationRecipients($isPolice);

        //get the correct document and email subject line, depending on whether the email is police
        if ($isPolice === 'Y') {
            $document = $publication->getPoliceDocument();
            $subject = self::EMAIL_POLICE_SUBJECT;
        } else {
            $document = $publication->getDocument();
            $subject = self::EMAIL_SUBJECT;
        }

        $templateData = ['filename' => basename($document->getFilename())];

        $message = new Message(self::TO_EMAIL, $subject);
        $message->setBcc($recipients);
        $message->setDocs([$document->getId()]);

        $subjectVars = [
            $publication->getPubType(),
            $publication->getPublicationNo(),
            $trafficArea->getName()
        ];

        //email subject line
        $message->setSubjectVariables($subjectVars);

        $this->sendEmailTemplate($message, self::EMAIL_TEMPLATE, $templateData);

        $result = new Result();
        $result->addMessage('Publication email sent');

        return $result;
    }
}
