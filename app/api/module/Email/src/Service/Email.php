<?php

namespace Dvsa\Olcs\Email\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail as ZendMail;
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
     *
     * @param string $fromEmail From email address
     * @param string $fromName  From name
     * @param string $to        To address
     * @param string $subject   Email subject
     * @param string $body      Email body
     * @param bool   $isHtml    whether to send as a HTML email
     * @param array  $cc        cc'd addresses
     */
    public function send($fromEmail, $fromName, $to, $subject, $body, $isHtml = false, array $cc = [])
    {
        if ((bool) $isHtml) {
            $htmlPart = new \Zend\Mime\Part($body);
            $htmlPart->type = \Zend\Mime\Mime::TYPE_HTML;

            $body = new \Zend\Mime\Message();
            $body->addPart($htmlPart);
        }

        $mail = new ZendMail\Message();
        $mail->setFrom($fromEmail, $fromName);
        $mail->addTo($to);
        $mail->addCc($cc);

        $mail->setSubject($subject);
        $mail->setBody($body);

        $trans = $this->getMailTransport();
        $trans->send($mail);
    }
}
