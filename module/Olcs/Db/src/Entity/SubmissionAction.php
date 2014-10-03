<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SubmissionAction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission_action",
 *    indexes={
 *        @ORM\Index(name="IDX_7FC9D12CE1FD4933", columns={"submission_id"}),
 *        @ORM\Index(name="IDX_7FC9D12CB15EFB97", columns={"recipient_user_id"}),
 *        @ORM\Index(name="IDX_7FC9D12C2A98155E", columns={"sender_user_id"}),
 *        @ORM\Index(name="IDX_7FC9D12CDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_7FC9D12C65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class SubmissionAction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Comment4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Submission
     *
     * @var \Olcs\Db\Entity\Submission
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Submission", fetch="LAZY", inversedBy="submissionActions")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", nullable=false)
     */
    protected $submission;

    /**
     * Recipient user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="recipient_user_id", referencedColumnName="id", nullable=false)
     */
    protected $recipientUser;

    /**
     * Sender user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=false)
     */
    protected $senderUser;

    /**
     * Is decision
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Urgent
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="urgent", nullable=true)
     */
    protected $urgent;

    /**
     * Submission action status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="submission_action_status", length=100, nullable=false)
     */
    protected $submissionActionStatus;

    /**
     * Submission action type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="submission_action_type", length=45, nullable=false)
     */
    protected $submissionActionType;

    /**
     * Set the submission
     *
     * @param \Olcs\Db\Entity\Submission $submission
     * @return SubmissionAction
     */
    public function setSubmission($submission)
    {
        $this->submission = $submission;

        return $this;
    }

    /**
     * Get the submission
     *
     * @return \Olcs\Db\Entity\Submission
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * Set the recipient user
     *
     * @param \Olcs\Db\Entity\User $recipientUser
     * @return SubmissionAction
     */
    public function setRecipientUser($recipientUser)
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    /**
     * Get the recipient user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getRecipientUser()
    {
        return $this->recipientUser;
    }

    /**
     * Set the sender user
     *
     * @param \Olcs\Db\Entity\User $senderUser
     * @return SubmissionAction
     */
    public function setSenderUser($senderUser)
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    /**
     * Get the sender user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getSenderUser()
    {
        return $this->senderUser;
    }

    /**
     * Set the is decision
     *
     * @param string $isDecision
     * @return SubmissionAction
     */
    public function setIsDecision($isDecision)
    {
        $this->isDecision = $isDecision;

        return $this;
    }

    /**
     * Get the is decision
     *
     * @return string
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }

    /**
     * Set the urgent
     *
     * @param string $urgent
     * @return SubmissionAction
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * Get the urgent
     *
     * @return string
     */
    public function getUrgent()
    {
        return $this->urgent;
    }

    /**
     * Set the submission action status
     *
     * @param string $submissionActionStatus
     * @return SubmissionAction
     */
    public function setSubmissionActionStatus($submissionActionStatus)
    {
        $this->submissionActionStatus = $submissionActionStatus;

        return $this;
    }

    /**
     * Get the submission action status
     *
     * @return string
     */
    public function getSubmissionActionStatus()
    {
        return $this->submissionActionStatus;
    }

    /**
     * Set the submission action type
     *
     * @param string $submissionActionType
     * @return SubmissionAction
     */
    public function setSubmissionActionType($submissionActionType)
    {
        $this->submissionActionType = $submissionActionType;

        return $this;
    }

    /**
     * Get the submission action type
     *
     * @return string
     */
    public function getSubmissionActionType()
    {
        return $this->submissionActionType;
    }
}
