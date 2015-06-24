<?php

namespace Dvsa\Olcs\Email\Service;

use Zend\Http\Client as HttpClient;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;

/**
 * Class Client
 */
class Client
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * \Zend\I18n\Translator\TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $selfServeUri;

    /**
     * @var string
     */
    protected $sendAllMailTo;

    /**
     * @param string $baseUri
     * @return $this
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param \Zend\Http\Client $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the default from
     *
     * @param string $email
     * @param string $name
     */
    public function setDefaultFrom($email, $name)
    {
        $this->fromEmail = $email;
        $this->fromName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getDefaultFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     *
     * @return type\Zend\I18n\Translator\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Zend\I18n\Translator\TranslatorInterface $translator
     * @return \Dvsa\Olcs\Email\Service\Client
     */
    public function setTranslator(\Zend\I18n\Translator\TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
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
     * @return string
     */
    public function getSelfServeUri()
    {
        return $this->selfServeUri;
    }

    /**
     * @param string $selfServeUri
     * @return \Dvsa\Olcs\Email\Service\Client
     */
    public function setSelfServeUri($selfServeUri)
    {
        $this->selfServeUri = rtrim($selfServeUri, '/');
        return $this;
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

    /**
     * @return string
     */
    public function getSendAllMailTo()
    {
        return $this->sendAllMailTo;
    }

    /**
     * Set an email address where all email will be sent to, useful for testing
     *
     * @param string $sendAllMailTo
     * @return \Dvsa\Olcs\Email\Service\Client
     */
    public function setSendAllMailTo($sendAllMailTo)
    {
        $this->sendAllMailTo = $sendAllMailTo;
        return $this;
    }

    /**
     *
     * @param \Dvsa\Olcs\Email\Data\Message $message
     * @param array $subjectVariables
     *
     * @return boolean
     * @throws EmailNotSentException
     */
    public function sendEmail(\Dvsa\Olcs\Email\Data\Message $message)
    {
        if (empty($message->getBody())) {
            throw new \RuntimeException('No message body has been set');
        }

        $fromName = $message->getFromName();
        if ($fromName === null) {
            $fromName = $this->getDefaultFromName();
        }
        $fromEmail = $message->getFromEmail();
        if ($fromEmail === null) {
            $fromEmail = $this->getDefaultFromEmail();
        }
        // translate subject
        $message->setSubject($this->translate($message->getSubject(), $message->getLocale()));

        $to = $message->getTo();
        if ($this->getSendAllMailTo()) {
            $to = $this->getSendAllMailTo();
            $message->setSubject($message->getTo() .' : '. $message->getSubject());
        }

        $this->getHttpClient()->getRequest()
            ->setUri($this->getBaseUri())
            ->setMethod('POST')
            ->getPost()
            ->set('to', $to)
            ->set('subject', $message->getSubjectReplaceVariables())
            ->set('body', $this->replaceUris($this->translate($message->getBody(), $message->getLocale())))
            ->set('html', $message->getHtml())
            ->set('fromEmail', $fromEmail)
            ->set('fromName', $fromName);

        $response = $this->getHttpClient()->send();
        if ($response->getStatusCode() != 202) {
            $message = 'Unknown error sending email';
            $jsonResponse = json_decode($response->getBody());
            if (isset($jsonResponse->errorMessage)) {
                $message = $jsonResponse->errorMessage;
            }
            throw new EmailNotSentException($message);
        }

        return true;
    }
}
