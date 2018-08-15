<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;

/**
 * Send ECMT app submitted email
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtAppSubmitted extends AbstractCommandHandler implements EmailAwareInterface
{
    use EmailAwareTrait;

    protected $repoServiceName = 'EcmtPermitApplication';

    private $template = 'ecmt-app-submitted';
    private $subject = 'email.ecmt.submitted.subject';

    /**
     * @var Message
     */
    private $message;

    /**
     * Sends email comfirming ecmt application has been submitted
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Dvsa\Olcs\Email\Exception\EmailNotSentException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplicationRepo   $repo
         * @var EcmtPermitApplicationEntity $application
         * @var SendEcmtAppSubmittedCmd     $command
         */
        $repo = $this->getRepo();
        $application = $repo->fetchUsingId($command);

        //$userEmail = $application->getCreatedBy()->getContactDetails()->getEmailAddress();
        //$orgEmailAddresses = $application->getLicence()->getOrganisation()->getAdminEmailAddresses();
        $orgEmailAddresses = [];
        $userEmail = 'terry.valtech@gmail.com';

        $templateVariables = [
            // http://selfserve is replaced based on the environment
            'url' => 'http://selfserve/',
            'applicationRef' => $application->getApplicationRef(),
        ];

        $subjectVariables = [
            'applicationRef' => $application->getApplicationRef(),
        ];

        $message = new Message($userEmail, $this->subject);
        $message->setSubjectVariables($subjectVariables);
        $message->setCc($orgEmailAddresses);

        $this->sendEmailTemplate($message, $this->template, $templateVariables);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $application->getId());
        $result->addMessage('Email sent');

        return $result;
    }

    /**
     * Returns the message object, used to assist with UT
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
