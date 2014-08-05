<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EbsrSubmissionResult Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ebsr_submission_result")
 */
class EbsrSubmissionResult implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Description100Field;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=64)
     */
    protected $id;

    /**
     * Email subject
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_subject", length=45, nullable=true)
     */
    protected $emailSubject;

    /**
     * Email body template
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_body_template", length=255, nullable=true)
     */
    protected $emailBodyTemplate;

    /**
     * Email authority
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="email_authority", nullable=false)
     */
    protected $emailAuthority = 0;

    /**
     * Set the id
     *
     * @param string $id
     * @return \Olcs\Db\Entity\EbsrSubmissionResult
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the email subject
     *
     * @param string $emailSubject
     * @return \Olcs\Db\Entity\EbsrSubmissionResult
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    /**
     * Get the email subject
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * Set the email body template
     *
     * @param string $emailBodyTemplate
     * @return \Olcs\Db\Entity\EbsrSubmissionResult
     */
    public function setEmailBodyTemplate($emailBodyTemplate)
    {
        $this->emailBodyTemplate = $emailBodyTemplate;

        return $this;
    }

    /**
     * Get the email body template
     *
     * @return string
     */
    public function getEmailBodyTemplate()
    {
        return $this->emailBodyTemplate;
    }

    /**
     * Set the email authority
     *
     * @param boolean $emailAuthority
     * @return \Olcs\Db\Entity\EbsrSubmissionResult
     */
    public function setEmailAuthority($emailAuthority)
    {
        $this->emailAuthority = $emailAuthority;

        return $this;
    }

    /**
     * Get the email authority
     *
     * @return boolean
     */
    public function getEmailAuthority()
    {
        return $this->emailAuthority;
    }
}
