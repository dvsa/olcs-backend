<?php

namespace Dvsa\Olcs\Email\Service;

use Dvsa\Olcs\Email\Exception\EmailNotSentException;
use Dvsa\Olcs\Email\Transport\MultiTransport;
use Dvsa\Olcs\Email\Transport\MultiTransportOptions;
use Dvsa\Olcs\Email\Transport\S3File;
use Dvsa\Olcs\Email\Transport\S3FileOptions;
use Laminas\Mail\Header\GenericHeader;
use Laminas\Mail\Transport\Factory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Mail as LaminasMail;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as LaminasMimePart;
use Laminas\Mime\Mime as LaminasMime;
use Laminas\Mail\Address as LaminasAddress;
use Laminas\Mail\AddressList;
use Laminas\Mail\Exception\InvalidArgumentException as LaminasAddressException;
use Laminas\Mail\Exception\RuntimeException as LaminasMailRuntimeException;
use Laminas\Mail\Transport\TransportInterface;
use Olcs\Logging\Log\Logger;
use Psr\Container\ContainerInterface;

/**
 * Class Email
 *
 * @package Olcs\Email\Service
 * @author  Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class Email implements FactoryInterface
{
    public const MISSING_FROM_ERROR = 'Email is missing a valid from address';
    public const MISSING_TO_ERROR = 'Email is missing a valid to address';
    public const NOT_SENT_ERROR = 'Email not sent: %s';

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
     * @param TransportInterface $mailTransport mail transport
     *
     * @return $this
     */
    public function setMailTransport(TransportInterface $mailTransport)
    {
        $this->mailTransport = $mailTransport;

        return $this;
    }

    /**
     * Validates the array of email addresses, excluding those which fail, and returns an AddressList object
     *
     * The array of cc/bcc can either be in the format [email_address => name] or [0 => email_address]
     * If the key is a string, it is assumed that is the email address, and the value is the name of the recipient
     *
     * The "to" address tends to just be a string, but we're designed here to cope if an array is passed in
     *
     * @param string|array|null $addressOrAddresses email addresses
     *
     * @return AddressList
     */
    public function validateAddresses($addressOrAddresses)
    {
        $addressList = new AddressList();

        //null or empty string
        if (empty($addressOrAddresses)) {
            return $addressList;
        }

        //addresses we pass as string, usually a to address
        if (!is_array($addressOrAddresses)) {
            $addressOrAddresses = [$addressOrAddresses];
        }

        //addresses passed in as an array (from, cc, bcc)
        foreach ($addressOrAddresses as $key => $value) {
            $email = null;

            if (is_int($key) || is_numeric($key)) {
                $email = $value;
            } elseif (is_string($key)) {
                $email = $key;
            }

            try {
                //olcs-14825 we no longer pass in the name, as this occasionally caused problems with postfix
                $address = new LaminasAddress($email);
                $addressList->add($address);
            } catch (LaminasAddressException) {
                //address is invalid in some way, right now these addresses are ignored
            }
        }

        return $addressList;
    }

    /**
     * Sends an email
     *
     * @param string $fromEmail From email address
     * @param string $fromName  From name
     * @param string $to        To address
     * @param string $subject   Email subject
     * @param string $plainBody Plain text email body
     * @param string $htmlBody  HTML email body
     * @param array  $cc        cc'd addresses
     * @param array  $bcc       bcc'd addresses
     * @param array  $docs      attached documents
     *
     * @return void
     * @throws EmailNotSentException
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
        array $docs = [],
        bool $highPriority = false
    ) {
        $emailBody = new MimeMessage();

        $fromAddress = $this->validateAddresses([$fromEmail => $fromName]);

        if (count($fromAddress) === 0) {
            Logger::err('email failed', ['data' => self::MISSING_FROM_ERROR]);
            throw new EmailNotSentException(self::MISSING_FROM_ERROR);
        }

        $toAddresses = $this->validateAddresses($to);

        if (count($toAddresses) === 0) {
            Logger::err('email failed', ['data' => self::MISSING_TO_ERROR, 'to' => $to]);
            throw new EmailNotSentException(self::MISSING_TO_ERROR);
        }

        $mail = new LaminasMail\Message();
        $mail->setFrom($fromAddress);
        $mail->addTo($toAddresses);
        $mail->addCc($this->validateAddresses($cc));
        $mail->addBcc($this->validateAddresses($bcc));
        $mail->setSubject($subject);

        $plainPart = new LaminasMimePart($plainBody);
        $plainPart->encoding = LaminasMime::ENCODING_QUOTEDPRINTABLE;
        $plainPart->type = LaminasMime::TYPE_TEXT;

        //if we've no html version we can safely send a plain text email without attachments
        //the only current (and likely future) use case for plain text only is the inspection request email
        if ($htmlBody === null) {
            $emailBody->addPart($plainPart);
            $messageType = LaminasMime::TYPE_TEXT;
        } else {
            $htmlPart = new LaminasMimePart($htmlBody);
            $htmlPart->encoding = LaminasMime::ENCODING_QUOTEDPRINTABLE;
            $htmlPart->type = LaminasMime::TYPE_HTML;

            $parts = [$plainPart, $htmlPart];

            if (!empty($docs)) {
                $messageType = LaminasMime::MULTIPART_MIXED;

                $messageBody = new MimeMessage();
                $messageBody->setParts($parts);

                $messagePart = new LaminasMimePart($messageBody->generateMessage());
                $messagePart->type = LaminasMime::MULTIPART_ALTERNATIVE . ";\n boundary=\"" .
                    $messageBody->getMime()->boundary() . '"';

                $emailBody->addPart($messagePart);

                foreach ($docs as $doc) {
                    $attachment = new LaminasMimePart($doc['content']);
                    $attachment->filename = $doc['fileName'];
                    $attachment->type = LaminasMime::TYPE_OCTETSTREAM;
                    $attachment->encoding = LaminasMime::ENCODING_BASE64;
                    $attachment->disposition = LaminasMime::DISPOSITION_ATTACHMENT;
                    $emailBody->addPart($attachment);
                }
            } else {
                $emailBody->setParts($parts);
                $messageType = LaminasMime::MULTIPART_ALTERNATIVE;
            }
        }

        $mail->setBody($emailBody);
        $mail->getHeaders()->get('content-type')->setType($messageType);

        if ($highPriority) {
            $this->setHighPriority($mail);
        }

        $trans = $this->getMailTransport();

        try {
            $trans->send($mail);
        } catch (\Exception $e) {
            $message = sprintf(self::NOT_SENT_ERROR, $e->getMessage());
            Logger::err('email failed', ['data' => $message]);
            throw new EmailNotSentException($message, 0, $e);
        }
    }

    private function setHighPriority(LaminasMail\Message $mail): void
    {
        $headers = $mail->getHeaders();
        $importanceHeader = new GenericHeader('Importance', 'High');
        $priorityHeader = new GenericHeader('X-Priority', '1');
        $msPriorityHeader = new GenericHeader('X-MSMail-Priority', 'High');
        $headers->addHeaders([$importanceHeader, $priorityHeader, $msPriorityHeader]);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        if (!isset($config['mail'])) {
            throw new LaminasMailRuntimeException('No mail config found');
        }
        $transport = Factory::create($config['mail']);
        if ($transport instanceof MultiTransport && isset($config['mail']['options'])) {
            $s3Options = $container->get(S3FileOptions::class);
            $multiTransportOptions = new MultiTransportOptions($config['mail']['options'], $s3Options);
            $transport->setOptions($multiTransportOptions);
        }
        if ($transport instanceof S3File && isset($config['mail']['options'])) {
            $transport->setOptions($container->get(S3FileOptions::class));
        }
        $this->setMailTransport($transport);
        return $this;
    }
}
