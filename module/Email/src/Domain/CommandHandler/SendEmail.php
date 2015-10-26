<?php

/**
 * Send Email
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Email\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Email\Domain\Command\SendEmail as Cmd;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;
use Zend\I18n\Translator\TranslatorInterface;
use Dvas\Olcs\Email\Service\Email as EmailService;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Send Email
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendEmail extends AbstractCommandHandler
{
    /**
     * @var string
     */
    private $fromName;

    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $sendAllMailTo;

    /**
     * @var string
     */
    private $selfServeUri;

    /**
     * @var EmailService
     */
    private $emailService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $config = $mainServiceLocator->get('Config');

        if (isset($config['email']['from_name'])) {
            $this->setFromName($config['email']['from_name']);
        }

        if (isset($config['email']['from_email'])) {
            $this->setFromEmail($config['email']['from_email']);
        }

        if (isset($config['email']['send_all_mail_to'])) {
            $this->setSendAllMailTo($config['email']['send_all_mail_to']);
        }

        if (isset($config['email']['selfserve_uri'])) {
            $this->setSelfServeUri($config['email']['selfserve_uri']);
        }

        $this->setTranslator($mainServiceLocator->get('translator'));

        $this->setEmailService($mainServiceLocator->get('EmailService'));

        return parent::createService($serviceLocator);
    }

    /**
     * @return string
     */
    public function getSelfServeUri()
    {
        return $this->selfServeUri;
    }

    /**
     * @param string $selfServeUri
     */
    public function setSelfServeUri($selfServeUri)
    {
        $this->selfServeUri = rtrim($selfServeUri, '/');
    }

    /**
     * @return string
     */
    public function getSendAllMailTo()
    {
        return $this->sendAllMailTo;
    }

    /**
     * @param string $sendAllMailTo
     */
    public function setSendAllMailTo($sendAllMailTo)
    {
        $this->sendAllMailTo = $sendAllMailTo;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return EmailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * @param EmailService $emailService
     */
    public function setEmailService(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * @param Cmd $command
     * @return bool
     * @throws EmailNotSentException
     */
    public function handleCommand(CommandInterface $command)
    {
        if (empty($command->getBody())) {
            throw new \RuntimeException('No message body has been set');
        }

        $fromName = $command->getFromName();

        if ($fromName === null) {
            $fromName = $this->getFromName();
        }

        $fromEmail = $command->getFromEmail();
        if ($fromEmail === null) {
            $fromEmail = $this->getFromEmail();
        }

        $subject = $this->translate($command->getSubject(), $command->getLocale());

        $to = $command->getTo();

        if ($this->getSendAllMailTo()) {
            $to = $this->getSendAllMailTo();

            $originalTo = implode(', ', (array)$command->getTo());

            $subject = $originalTo .' : '. $subject;
        }

        $subject = vsprintf($subject, $command->getSubjectVariables());

        $body = $this->replaceUris($this->translate($command->getBody(), $command->getLocale()));

        $this->send($to, $subject, $body, $command->getHtml(), $fromEmail, $fromName);

        $this->result->addMessage('Email sent');
        return $this->result;
    }

    protected function send($to, $subject, $body, $isHtml, $fromEmail, $fromName)
    {
        $this->getEmailService()->send($fromEmail, $fromName, $to, $subject, $body, $isHtml);
    }

    /**
     * Translate a message
     *
     * @param string $message
     * @param string $locale
     *
     * @return string
     */
    protected function translate($message, $locale = 'en_GB')
    {
        if ($this->getTranslator()) {
            return $this->getTranslator()->translate($message, 'email', $locale);
        }
        return $message;
    }

    /**
     * Replace "selfserve" URI with the real URI
     *
     * @param string $text
     *
     * @return string
     */
    protected function replaceUris($text)
    {
        return str_replace('http://selfserve', $this->getSelfServeUri(), $text);
    }
}
