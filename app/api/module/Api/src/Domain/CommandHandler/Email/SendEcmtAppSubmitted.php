<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\MissingEmailException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;

/**
 * Send ECMT app submitted email
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SendEcmtAppSubmitted extends AbstractCommandHandler implements EmailAwareInterface, ToggleRequiredInterface
{
    use EmailAwareTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';

    const TEMPLATE = 'ecmt-app-submitted';
    const SUBJECT = 'email.ecmt.submitted.subject';

    /**
     * @var Message
     */
    private $message;

    /**
     * Sends email confirming ecmt application has been submitted
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
        $result = new Result();
        $result->addId('ecmtPermitApplication', $application->getId());

        try {
            $recipients = $this->recipientsForPermitApplication($application);
        } catch (MissingEmailException $e) {
            /** @todo check behaviour on this for future - inform someone, create task perhaps? */
            $result->addMessage($e->getMessage());
            return $result;
        }

        $applicationRef = $application->getApplicationRef();

        $templateVariables = [
            // http://selfserve is replaced based on the environment
            'url' => 'http://selfserve/',
            'applicationRef' => $applicationRef,
        ];

        $subjectVariables = [
            'applicationRef' => $applicationRef,
        ];

        $this->message = new Message($recipients['to'], self::SUBJECT);
        $this->message->setSubjectVariables($subjectVariables);
        $this->message->setCc($recipients['cc']);

        $this->sendEmailTemplate($this->message, self::TEMPLATE, $templateVariables);

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
