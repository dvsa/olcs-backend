<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="ix_submission_action_sender_user_id", columns={"sender_user_id"}),
 *        @ORM\Index(name="ix_submission_action_recipient_user_id", columns={"recipient_user_id"}),
 *        @ORM\Index(name="ix_submission_action_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_submission_action_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_submission_action_submission_id", columns={"submission_id"}),
 *        @ORM\Index(name="ix_submission_action_submission_action_status", columns={"submission_action_status"})
 *    }
 * )
 */
class SubmissionAction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CommentField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is decision
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Reason", inversedBy="submissionActions")
     * @ORM\JoinTable(name="submission_action_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="submission_action_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $reasons;

    /**
     * Recipient user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="recipient_user_id", referencedColumnName="id", nullable=false)
     */
    protected $recipientUser;

    /**
     * Sender user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=false)
     */
    protected $senderUser;

    /**
     * Submission
     *
     * @var \Olcs\Db\Entity\Submission
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Submission", inversedBy="submissionActions")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", nullable=false)
     */
    protected $submission;

    /**
     * Submission action status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="submission_action_status", referencedColumnName="id", nullable=false)
     */
    protected $submissionActionStatus;

    /**
     * Urgent
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="urgent", nullable=true)
     */
    protected $urgent;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->reasons = new ArrayCollection();
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
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return SubmissionAction
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;

        return $this;
    }

    /**
     * Get the reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Add a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return SubmissionAction
     */
    public function addReasons($reasons)
    {
        if ($reasons instanceof ArrayCollection) {
            $this->reasons = new ArrayCollection(
                array_merge(
                    $this->reasons->toArray(),
                    $reasons->toArray()
                )
            );
        } elseif (!$this->reasons->contains($reasons)) {
            $this->reasons->add($reasons);
        }

        return $this;
    }

    /**
     * Remove a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return SubmissionAction
     */
    public function removeReasons($reasons)
    {
        if ($this->reasons->contains($reasons)) {
            $this->reasons->removeElement($reasons);
        }

        return $this;
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
     * Set the submission action status
     *
     * @param \Olcs\Db\Entity\RefData $submissionActionStatus
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getSubmissionActionStatus()
    {
        return $this->submissionActionStatus;
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
}
