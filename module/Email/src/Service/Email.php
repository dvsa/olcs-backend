<?php

namespace Dvsa\Olcs\Email\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail as ZendMail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as ZendMimePart;
use Zend\Mime\Mime as ZendMime;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Exception\RuntimeException as ZendMailTransportRuntimeException;
use Zend\Mail\Exception\RuntimeException as ZendMailRuntimeException;
use Zend\Mail\Transport\TransportInterface;

/**
 * Class Email
 *
 * @package Olcs\Email\Service
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class Email implements FactoryInterface
{
    private $mailTransport;

    /**
     * Get Transport.
     *
     * @return TransportInterface
     */
    public function getMailTransport()
    {
        return $this->mailTransport;
    }

    /**
     * Set Transport.
     *
     * @param TransportInterface $mailTransport
     * @return $this
     */
    public function setMailTransport(TransportInterface $mailTransport)
    {
        $this->mailTransport = $mailTransport;

        return $this;
    }

    /**
     * Setup the factory, with a service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['mail'])) {
            throw new ZendMailRuntimeException('No mail config found');
        }

        $transport = ZendMail\Transport\Factory::create($config['mail']);
        $this->setMailTransport($transport);

        return $this;
    }

    /**
     * @todo we should support plain text alternatives to our HTML emails, have raised with Sam
     *
     * @param string $fromEmail From email address
     * @param string $fromName  From name
     * @param string $to        To address
     * @param string $subject   Email subject
     * @param string $body      Email body
     * @param bool   $isHtml    whether to send as a HTML email
     * @param array  $cc        cc'd addresses
     * @param array  $bcc       bcc'd addresses
     * @param array  $docs      attached documents
     */
    public function send(
        $fromEmail,
        $fromName,
        $to,
        $subject,
        $body,
        $isHtml = false,
        array $cc = [],
        array $bcc = [],
        array $docs = []
    ) {
        $messageType = (bool)$isHtml ? ZendMime::TYPE_HTML : ZendMime::TYPE_TEXT;

        $messagePart = new ZendMimePart($body);
        $messagePart->encoding = ZendMime::ENCODING_QUOTEDPRINTABLE;
        $messagePart->type = $messageType;

        $emailBody = new MimeMessage();
        $emailBody->addPart($messagePart);

        if (!empty($docs)) {
            $messageType = ZendMime::MULTIPART_MIXED;

            foreach ($docs as $doc) {
                $attachment = new ZendMimePart($doc['content']);
                $attachment->filename    = $doc['fileName'];
                $attachment->type        = ZendMime::TYPE_OCTETSTREAM;
                $attachment->encoding    = ZendMime::ENCODING_BASE64;
                $attachment->disposition = ZendMime::DISPOSITION_ATTACHMENT;
                $emailBody->addPart($attachment);
            }
        }

        $mail = new ZendMail\Message();
        $mail->setFrom($fromEmail, $fromName);
        $mail->addTo($to);
        $mail->addCc($cc);
        $mail->addBcc($bcc);
        $mail->setSubject($subject);
        $mail->setBody($emailBody);
        $mail->getHeaders()->get('content-type')->setType($messageType);

        $trans = $this->getMailTransport();
        $trans->send($mail);
    }
}
