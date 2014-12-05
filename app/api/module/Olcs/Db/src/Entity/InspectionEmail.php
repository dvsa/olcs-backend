<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * InspectionEmail Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_email",
 *    indexes={
 *        @ORM\Index(name="fk_ea_inspection_email_inspection_request1_idx", columns={"inspection_request_id"})
 *    }
 * )
 */
class InspectionEmail implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ReceivedDateFieldAlt1;

    /**
     * Inspection request
     *
     * @var \Olcs\Db\Entity\InspectionRequest
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\InspectionRequest")
     * @ORM\JoinColumn(name="inspection_request_id", referencedColumnName="id", nullable=false)
     */
    protected $inspectionRequest;

    /**
     * Subject
     *
     * @var string
     *
     * @ORM\Column(type="string", name="subject", length=1024, nullable=false)
     */
    protected $subject;

    /**
     * Message body
     *
     * @var string
     *
     * @ORM\Column(type="text", name="message_body", length=16777215, nullable=true)
     */
    protected $messageBody;

    /**
     * Email status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_status", length=1, nullable=false)
     */
    protected $emailStatus;

    /**
     * Processed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="processed", nullable=false)
     */
    protected $processed = 0;

    /**
     * Sender email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sender_email_address", length=200, nullable=true)
     */
    protected $senderEmailAddress;

    /**
     * Set the inspection request
     *
     * @param \Olcs\Db\Entity\InspectionRequest $inspectionRequest
     * @return InspectionEmail
     */
    public function setInspectionRequest($inspectionRequest)
    {
        $this->inspectionRequest = $inspectionRequest;

        return $this;
    }

    /**
     * Get the inspection request
     *
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function getInspectionRequest()
    {
        return $this->inspectionRequest;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     * @return InspectionEmail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the message body
     *
     * @param string $messageBody
     * @return InspectionEmail
     */
    public function setMessageBody($messageBody)
    {
        $this->messageBody = $messageBody;

        return $this;
    }

    /**
     * Get the message body
     *
     * @return string
     */
    public function getMessageBody()
    {
        return $this->messageBody;
    }

    /**
     * Set the email status
     *
     * @param string $emailStatus
     * @return InspectionEmail
     */
    public function setEmailStatus($emailStatus)
    {
        $this->emailStatus = $emailStatus;

        return $this;
    }

    /**
     * Get the email status
     *
     * @return string
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    /**
     * Set the processed
     *
     * @param string $processed
     * @return InspectionEmail
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get the processed
     *
     * @return string
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set the sender email address
     *
     * @param string $senderEmailAddress
     * @return InspectionEmail
     */
    public function setSenderEmailAddress($senderEmailAddress)
    {
        $this->senderEmailAddress = $senderEmailAddress;

        return $this;
    }

    /**
     * Get the sender email address
     *
     * @return string
     */
    public function getSenderEmailAddress()
    {
        return $this->senderEmailAddress;
    }
}
