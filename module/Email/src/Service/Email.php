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
     * @param string $fromEmail From email address
     * @param string $fromName  From name
     * @param string $to        To address
     * @param string $subject   Email subject
     * @param string $plainBody Plain text email body
     * @param string $htmlBody  HTML email body
     * @param array  $cc        cc'd addresses
     * @param array  $bcc       bcc'd addresses
     * @param array  $docs      attached documents
     */
    public function send(
        $fromEmail,
        $fromName,
        $to,
        $subject,
        $plainBody,
        $htmlBody,
        array $cc = [],
        array $bcc = [],
        array $docs = []
    ) {
        $emailBody = new MimeMessage();

        $mail = new ZendMail\Message();
        $mail->setFrom($fromEmail, $fromName);
        $mail->addTo($to);
        $mail->addCc($cc);
        $mail->addBcc($bcc);
        $mail->setSubject($subject);

        $plainPart = new ZendMimePart($plainBody);
        $plainPart->encoding = ZendMime::ENCODING_QUOTEDPRINTABLE;
        $plainPart->type = ZendMime::TYPE_TEXT;

        //if we've no html version we can safely send a plain text email without attachments
        //the only current (and likely future) use case for plain text only is the inspection request email
        if ($htmlBody === null) {
            $emailBody->addPart($plainPart);
            $messageType = ZendMime::TYPE_TEXT;
        } else {
            $htmlPart = new ZendMimePart($htmlBody);
            $htmlPart->encoding = ZendMime::ENCODING_QUOTEDPRINTABLE;
            $htmlPart->type = ZendMime::TYPE_HTML;

            $parts = [$plainPart, $htmlPart];

            if (!empty($docs)) {
                $messageType = ZendMime::MULTIPART_MIXED;

                $messageBody = new MimeMessage();
                $messageBody->setParts($parts);

                $messagePart = new ZendMimePart($messageBody->generateMessage());
                $messagePart->type = ZendMime::MULTIPART_ALTERNATIVE . ";\n boundary=\"" .
                    $messageBody->getMime()->boundary() . '"';

                $emailBody->addPart($messagePart);

                foreach ($docs as $doc) {
                    $attachment = new ZendMimePart($doc['content']);
                    $attachment->filename = $doc['fileName'];
                    $attachment->type = ZendMime::TYPE_OCTETSTREAM;
                    $attachment->encoding = ZendMime::ENCODING_BASE64;
                    $attachment->disposition = ZendMime::DISPOSITION_ATTACHMENT;
                    $emailBody->addPart($attachment);
                }
            } else {
                $emailBody->setParts($parts);
                $messageType = ZendMime::MULTIPART_ALTERNATIVE;
            }
        }

        $mail->setBody($emailBody);
        $mail->getHeaders()->get('content-type')->setType($messageType);

        $trans = $this->getMailTransport();
        $trans->send($mail);
    }
}
