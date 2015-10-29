<?php

namespace Dvsa\Olcs\Email\Data;

use Dvsa\Olcs\Email\Domain\Command\SendEmail;

/**
 * Email Message
 *
 * @NOTE This has been left in for backwards compatibility, this essentially creates a command
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Message
{
    protected $fromName;

    protected $fromEmail;

    protected $to;

    protected $subject;

    protected $subjectVariables = [];

    protected $body;

    protected $html = true;

    protected $locale = 'en_GB';

    public function __construct($to, $subject)
    {
        $this->setTo($to);
        $this->setSubject($subject);
    }

    public function buildCommand()
    {
        $data = [
            'fromName' => $this->getFromName(),
            'fromEmail' => $this->getFromEmail(),
            'to' => $this->getTo(),
            'subject' => $this->getSubject(),
            'subjectVariables' => $this->getSubjectVariables(),
            'body' => $this->getBody(),
            'html' => $this->getHtml(),
            'locale' => $this->getLocale()
        ];

        return SendEmail::create($data);
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getSubjectReplaceVariables()
    {
        return vsprintf($this->getSubject(), $this->getSubjectVariables());
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     *
     * @param string $fromName
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @param string $fromEmail
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * @param string $to
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param string $subject
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $body
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param bool $html
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubjectVariables()
    {
        return $this->subjectVariables;
    }

    /**
     * Set variables to be replace in the subject
     * NB uses sprintf
     *
     * @param array $subjectVariables [var1, var2, etc]
     *
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setSubjectVariables(array $subjectVariables)
    {
        $this->subjectVariables = $subjectVariables;
        return $this;
    }

    /**
     * Translate message into Welsh
     *
     * @param string $yesNo 'Y' or 'N'
     *
     * @return \Dvsa\Olcs\Email\Data\Message
     */
    public function setTranslateToWelsh($yesNo)
    {
        if (strtoupper($yesNo) === 'Y') {
            $this->setLocale('cy_GB');
        }
        return $this;
    }
}
