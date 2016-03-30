<?php

/**
 * Send Email
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Email\Domain\Command;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Send Email
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendEmail extends AbstractCommand
{
    protected $fromName;

    protected $fromEmail;

    protected $to;

    protected $cc = [];

    protected $bcc = [];

    protected $docs = [];

    protected $subject;

    protected $subjectVariables;

    protected $body;

    protected $html = true;

    protected $locale = 'en_GB';

    public function getFromName()
    {
        return $this->fromName;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getCc()
    {
        return $this->cc;
    }

    public function getBcc()
    {
        return $this->bcc;
    }

    public function getDocs()
    {
        return $this->docs;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getSubjectVariables()
    {
        return $this->subjectVariables;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
