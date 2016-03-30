<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Pi Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="pi",
 *    indexes={
 *        @ORM\Index(name="ix_pi_pi_status", columns={"pi_status"}),
 *        @ORM\Index(name="ix_pi_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_pi_assigned_to", columns={"assigned_to"}),
 *        @ORM\Index(name="ix_pi_agreed_by_tc_id", columns={"agreed_by_tc_id"}),
 *        @ORM\Index(name="ix_pi_decided_by_tc_id", columns={"decided_by_tc_id"}),
 *        @ORM\Index(name="ix_pi_agreed_by_tc_role", columns={"agreed_by_tc_role"}),
 *        @ORM\Index(name="ix_pi_decided_by_tc_role", columns={"decided_by_tc_role"}),
 *        @ORM\Index(name="ix_pi_written_outcome", columns={"written_outcome"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_pi_case_id", columns={"case_id"}),
 *        @ORM\UniqueConstraint(name="uk_pi_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
abstract class AbstractPi implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Agreed by tc
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="agreed_by_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $agreedByTc;

    /**
     * Agreed by tc role
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="agreed_by_tc_role", referencedColumnName="id", nullable=true)
     */
    protected $agreedByTcRole;

    /**
     * Agreed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="agreed_date", nullable=true)
     */
    protected $agreedDate;

    /**
     * Assigned to
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id", nullable=true)
     */
    protected $assignedTo;

    /**
     * Brief to tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="brief_to_tc_date", nullable=true)
     */
    protected $briefToTcDate;

    /**
     * Call up letter date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="call_up_letter_date", nullable=true)
     */
    protected $callUpLetterDate;

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\OneToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases",
     *     fetch="LAZY",
     *     inversedBy="publicInquiry"
     * )
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Closed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="closed_date", nullable=true)
     */
    protected $closedDate;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="string", name="comment", length=4000, nullable=true)
     */
    protected $comment;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Decided by tc
     *
     * @var \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="decided_by_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $decidedByTc;

    /**
     * Decided by tc role
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="decided_by_tc_role", referencedColumnName="id", nullable=true)
     */
    protected $decidedByTcRole;

    /**
     * Decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_date", nullable=true)
     */
    protected $decisionDate;

    /**
     * Decision letter sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_letter_sent_date", nullable=true)
     */
    protected $decisionLetterSentDate;

    /**
     * Decision notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="decision_notes", length=65535, nullable=true)
     */
    protected $decisionNotes;

    /**
     * Decision
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\Pi\Decision", inversedBy="pis", fetch="LAZY")
     * @ORM\JoinTable(name="pi_decision",
     *     joinColumns={
     *         @ORM\JoinColumn(name="pi_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="decision_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $decisions;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Is cancelled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_cancelled", nullable=false, options={"default": 0})
     */
    protected $isCancelled = 0;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Licence curtailed at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="licence_curtailed_at_pi",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $licenceCurtailedAtPi = 0;

    /**
     * Licence revoked at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="licence_revoked_at_pi",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $licenceRevokedAtPi = 0;

    /**
     * Licence suspended at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="licence_suspended_at_pi",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $licenceSuspendedAtPi = 0;

    /**
     * Notification date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="notification_date", nullable=true)
     */
    protected $notificationDate;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=32, nullable=true)
     */
    protected $olbsType;

    /**
     * Pi status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_status", referencedColumnName="id", nullable=false)
     */
    protected $piStatus;

    /**
     * Pi type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\RefData",
     *     inversedBy="pis",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="pi_type",
     *     joinColumns={
     *         @ORM\JoinColumn(name="pi_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="pi_type_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $piTypes;

    /**
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\Pi\Reason", inversedBy="pis", fetch="LAZY")
     * @ORM\JoinTable(name="pi_reason",
     *     joinColumns={
     *         @ORM\JoinColumn(name="pi_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $reasons;

    /**
     * Tc written decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="tc_written_decision_date", nullable=true)
     */
    protected $tcWrittenDecisionDate;

    /**
     * Tc written reason date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="tc_written_reason_date", nullable=true)
     */
    protected $tcWrittenReasonDate;

    /**
     * Tm called with operator
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="tm_called_with_operator",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $tmCalledWithOperator = 0;

    /**
     * Tm decision
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\RefData",
     *     inversedBy="pis",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="pi_tm_decision",
     *     joinColumns={
     *         @ORM\JoinColumn(name="pi_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="tm_decision_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $tmDecisions;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="witnesses", nullable=true)
     */
    protected $witnesses;

    /**
     * Written decision letter date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="written_decision_letter_date", nullable=true)
     */
    protected $writtenDecisionLetterDate;

    /**
     * Written outcome
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="written_outcome", referencedColumnName="id", nullable=true)
     */
    protected $writtenOutcome;

    /**
     * Written reason date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="written_reason_date", nullable=true)
     */
    protected $writtenReasonDate;

    /**
     * Written reason letter date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="written_reason_letter_date", nullable=true)
     */
    protected $writtenReasonLetterDate;

    /**
     * Pi hearing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Pi\PiHearing", mappedBy="pi")
     */
    protected $piHearings;

    /**
     * Publication link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Publication\PublicationLink", mappedBy="pi")
     */
    protected $publicationLinks;

    /**
     * Sla target date
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\SlaTargetDate",
     *     mappedBy="pi",
     *     cascade={"persist"},
     *     indexBy="sla_id",
     *     orphanRemoval=true
     * )
     */
    protected $slaTargetDates;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->piTypes = new ArrayCollection();
        $this->tmDecisions = new ArrayCollection();
        $this->reasons = new ArrayCollection();
        $this->decisions = new ArrayCollection();
        $this->piHearings = new ArrayCollection();
        $this->publicationLinks = new ArrayCollection();
        $this->slaTargetDates = new ArrayCollection();
    }

    /**
     * Set the agreed by tc
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PresidingTc $agreedByTc
     * @return Pi
     */
    public function setAgreedByTc($agreedByTc)
    {
        $this->agreedByTc = $agreedByTc;

        return $this;
    }

    /**
     * Get the agreed by tc
     *
     * @return \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     */
    public function getAgreedByTc()
    {
        return $this->agreedByTc;
    }

    /**
     * Set the agreed by tc role
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $agreedByTcRole
     * @return Pi
     */
    public function setAgreedByTcRole($agreedByTcRole)
    {
        $this->agreedByTcRole = $agreedByTcRole;

        return $this;
    }

    /**
     * Get the agreed by tc role
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getAgreedByTcRole()
    {
        return $this->agreedByTcRole;
    }

    /**
     * Set the agreed date
     *
     * @param \DateTime $agreedDate
     * @return Pi
     */
    public function setAgreedDate($agreedDate)
    {
        $this->agreedDate = $agreedDate;

        return $this;
    }

    /**
     * Get the agreed date
     *
     * @return \DateTime
     */
    public function getAgreedDate()
    {
        return $this->agreedDate;
    }

    /**
     * Set the assigned to
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $assignedTo
     * @return Pi
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get the assigned to
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set the brief to tc date
     *
     * @param \DateTime $briefToTcDate
     * @return Pi
     */
    public function setBriefToTcDate($briefToTcDate)
    {
        $this->briefToTcDate = $briefToTcDate;

        return $this;
    }

    /**
     * Get the brief to tc date
     *
     * @return \DateTime
     */
    public function getBriefToTcDate()
    {
        return $this->briefToTcDate;
    }

    /**
     * Set the call up letter date
     *
     * @param \DateTime $callUpLetterDate
     * @return Pi
     */
    public function setCallUpLetterDate($callUpLetterDate)
    {
        $this->callUpLetterDate = $callUpLetterDate;

        return $this;
    }

    /**
     * Get the call up letter date
     *
     * @return \DateTime
     */
    public function getCallUpLetterDate()
    {
        return $this->callUpLetterDate;
    }

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @return Pi
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the closed date
     *
     * @param \DateTime $closedDate
     * @return Pi
     */
    public function setClosedDate($closedDate)
    {
        $this->closedDate = $closedDate;

        return $this;
    }

    /**
     * Get the closed date
     *
     * @return \DateTime
     */
    public function getClosedDate()
    {
        return $this->closedDate;
    }

    /**
     * Set the comment
     *
     * @param string $comment
     * @return Pi
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Pi
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return Pi
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the decided by tc
     *
     * @param \Dvsa\Olcs\Api\Entity\Pi\PresidingTc $decidedByTc
     * @return Pi
     */
    public function setDecidedByTc($decidedByTc)
    {
        $this->decidedByTc = $decidedByTc;

        return $this;
    }

    /**
     * Get the decided by tc
     *
     * @return \Dvsa\Olcs\Api\Entity\Pi\PresidingTc
     */
    public function getDecidedByTc()
    {
        return $this->decidedByTc;
    }

    /**
     * Set the decided by tc role
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $decidedByTcRole
     * @return Pi
     */
    public function setDecidedByTcRole($decidedByTcRole)
    {
        $this->decidedByTcRole = $decidedByTcRole;

        return $this;
    }

    /**
     * Get the decided by tc role
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getDecidedByTcRole()
    {
        return $this->decidedByTcRole;
    }

    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return Pi
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get the decision date
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }

    /**
     * Set the decision letter sent date
     *
     * @param \DateTime $decisionLetterSentDate
     * @return Pi
     */
    public function setDecisionLetterSentDate($decisionLetterSentDate)
    {
        $this->decisionLetterSentDate = $decisionLetterSentDate;

        return $this;
    }

    /**
     * Get the decision letter sent date
     *
     * @return \DateTime
     */
    public function getDecisionLetterSentDate()
    {
        return $this->decisionLetterSentDate;
    }

    /**
     * Set the decision notes
     *
     * @param string $decisionNotes
     * @return Pi
     */
    public function setDecisionNotes($decisionNotes)
    {
        $this->decisionNotes = $decisionNotes;

        return $this;
    }

    /**
     * Get the decision notes
     *
     * @return string
     */
    public function getDecisionNotes()
    {
        return $this->decisionNotes;
    }

    /**
     * Set the decision
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $decisions
     * @return Pi
     */
    public function setDecisions($decisions)
    {
        $this->decisions = $decisions;

        return $this;
    }

    /**
     * Get the decisions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDecisions()
    {
        return $this->decisions;
    }

    /**
     * Add a decisions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $decisions
     * @return Pi
     */
    public function addDecisions($decisions)
    {
        if ($decisions instanceof ArrayCollection) {
            $this->decisions = new ArrayCollection(
                array_merge(
                    $this->decisions->toArray(),
                    $decisions->toArray()
                )
            );
        } elseif (!$this->decisions->contains($decisions)) {
            $this->decisions->add($decisions);
        }

        return $this;
    }

    /**
     * Remove a decisions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $decisions
     * @return Pi
     */
    public function removeDecisions($decisions)
    {
        if ($this->decisions->contains($decisions)) {
            $this->decisions->removeElement($decisions);
        }

        return $this;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return Pi
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Pi
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the is cancelled
     *
     * @param string $isCancelled
     * @return Pi
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get the is cancelled
     *
     * @return string
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Pi
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return Pi
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the licence curtailed at pi
     *
     * @param string $licenceCurtailedAtPi
     * @return Pi
     */
    public function setLicenceCurtailedAtPi($licenceCurtailedAtPi)
    {
        $this->licenceCurtailedAtPi = $licenceCurtailedAtPi;

        return $this;
    }

    /**
     * Get the licence curtailed at pi
     *
     * @return string
     */
    public function getLicenceCurtailedAtPi()
    {
        return $this->licenceCurtailedAtPi;
    }

    /**
     * Set the licence revoked at pi
     *
     * @param string $licenceRevokedAtPi
     * @return Pi
     */
    public function setLicenceRevokedAtPi($licenceRevokedAtPi)
    {
        $this->licenceRevokedAtPi = $licenceRevokedAtPi;

        return $this;
    }

    /**
     * Get the licence revoked at pi
     *
     * @return string
     */
    public function getLicenceRevokedAtPi()
    {
        return $this->licenceRevokedAtPi;
    }

    /**
     * Set the licence suspended at pi
     *
     * @param string $licenceSuspendedAtPi
     * @return Pi
     */
    public function setLicenceSuspendedAtPi($licenceSuspendedAtPi)
    {
        $this->licenceSuspendedAtPi = $licenceSuspendedAtPi;

        return $this;
    }

    /**
     * Get the licence suspended at pi
     *
     * @return string
     */
    public function getLicenceSuspendedAtPi()
    {
        return $this->licenceSuspendedAtPi;
    }

    /**
     * Set the notification date
     *
     * @param \DateTime $notificationDate
     * @return Pi
     */
    public function setNotificationDate($notificationDate)
    {
        $this->notificationDate = $notificationDate;

        return $this;
    }

    /**
     * Get the notification date
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->notificationDate;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return Pi
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the olbs type
     *
     * @param string $olbsType
     * @return Pi
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
    }

    /**
     * Set the pi status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $piStatus
     * @return Pi
     */
    public function setPiStatus($piStatus)
    {
        $this->piStatus = $piStatus;

        return $this;
    }

    /**
     * Get the pi status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getPiStatus()
    {
        return $this->piStatus;
    }

    /**
     * Set the pi type
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piTypes
     * @return Pi
     */
    public function setPiTypes($piTypes)
    {
        $this->piTypes = $piTypes;

        return $this;
    }

    /**
     * Get the pi types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPiTypes()
    {
        return $this->piTypes;
    }

    /**
     * Add a pi types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piTypes
     * @return Pi
     */
    public function addPiTypes($piTypes)
    {
        if ($piTypes instanceof ArrayCollection) {
            $this->piTypes = new ArrayCollection(
                array_merge(
                    $this->piTypes->toArray(),
                    $piTypes->toArray()
                )
            );
        } elseif (!$this->piTypes->contains($piTypes)) {
            $this->piTypes->add($piTypes);
        }

        return $this;
    }

    /**
     * Remove a pi types
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piTypes
     * @return Pi
     */
    public function removePiTypes($piTypes)
    {
        if ($this->piTypes->contains($piTypes)) {
            $this->piTypes->removeElement($piTypes);
        }

        return $this;
    }

    /**
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return Pi
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
     * @return Pi
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
     * @return Pi
     */
    public function removeReasons($reasons)
    {
        if ($this->reasons->contains($reasons)) {
            $this->reasons->removeElement($reasons);
        }

        return $this;
    }

    /**
     * Set the tc written decision date
     *
     * @param \DateTime $tcWrittenDecisionDate
     * @return Pi
     */
    public function setTcWrittenDecisionDate($tcWrittenDecisionDate)
    {
        $this->tcWrittenDecisionDate = $tcWrittenDecisionDate;

        return $this;
    }

    /**
     * Get the tc written decision date
     *
     * @return \DateTime
     */
    public function getTcWrittenDecisionDate()
    {
        return $this->tcWrittenDecisionDate;
    }

    /**
     * Set the tc written reason date
     *
     * @param \DateTime $tcWrittenReasonDate
     * @return Pi
     */
    public function setTcWrittenReasonDate($tcWrittenReasonDate)
    {
        $this->tcWrittenReasonDate = $tcWrittenReasonDate;

        return $this;
    }

    /**
     * Get the tc written reason date
     *
     * @return \DateTime
     */
    public function getTcWrittenReasonDate()
    {
        return $this->tcWrittenReasonDate;
    }

    /**
     * Set the tm called with operator
     *
     * @param string $tmCalledWithOperator
     * @return Pi
     */
    public function setTmCalledWithOperator($tmCalledWithOperator)
    {
        $this->tmCalledWithOperator = $tmCalledWithOperator;

        return $this;
    }

    /**
     * Get the tm called with operator
     *
     * @return string
     */
    public function getTmCalledWithOperator()
    {
        return $this->tmCalledWithOperator;
    }

    /**
     * Set the tm decision
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmDecisions
     * @return Pi
     */
    public function setTmDecisions($tmDecisions)
    {
        $this->tmDecisions = $tmDecisions;

        return $this;
    }

    /**
     * Get the tm decisions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmDecisions()
    {
        return $this->tmDecisions;
    }

    /**
     * Add a tm decisions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmDecisions
     * @return Pi
     */
    public function addTmDecisions($tmDecisions)
    {
        if ($tmDecisions instanceof ArrayCollection) {
            $this->tmDecisions = new ArrayCollection(
                array_merge(
                    $this->tmDecisions->toArray(),
                    $tmDecisions->toArray()
                )
            );
        } elseif (!$this->tmDecisions->contains($tmDecisions)) {
            $this->tmDecisions->add($tmDecisions);
        }

        return $this;
    }

    /**
     * Remove a tm decisions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmDecisions
     * @return Pi
     */
    public function removeTmDecisions($tmDecisions)
    {
        if ($this->tmDecisions->contains($tmDecisions)) {
            $this->tmDecisions->removeElement($tmDecisions);
        }

        return $this;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return Pi
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the witnesses
     *
     * @param int $witnesses
     * @return Pi
     */
    public function setWitnesses($witnesses)
    {
        $this->witnesses = $witnesses;

        return $this;
    }

    /**
     * Get the witnesses
     *
     * @return int
     */
    public function getWitnesses()
    {
        return $this->witnesses;
    }

    /**
     * Set the written decision letter date
     *
     * @param \DateTime $writtenDecisionLetterDate
     * @return Pi
     */
    public function setWrittenDecisionLetterDate($writtenDecisionLetterDate)
    {
        $this->writtenDecisionLetterDate = $writtenDecisionLetterDate;

        return $this;
    }

    /**
     * Get the written decision letter date
     *
     * @return \DateTime
     */
    public function getWrittenDecisionLetterDate()
    {
        return $this->writtenDecisionLetterDate;
    }

    /**
     * Set the written outcome
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $writtenOutcome
     * @return Pi
     */
    public function setWrittenOutcome($writtenOutcome)
    {
        $this->writtenOutcome = $writtenOutcome;

        return $this;
    }

    /**
     * Get the written outcome
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getWrittenOutcome()
    {
        return $this->writtenOutcome;
    }

    /**
     * Set the written reason date
     *
     * @param \DateTime $writtenReasonDate
     * @return Pi
     */
    public function setWrittenReasonDate($writtenReasonDate)
    {
        $this->writtenReasonDate = $writtenReasonDate;

        return $this;
    }

    /**
     * Get the written reason date
     *
     * @return \DateTime
     */
    public function getWrittenReasonDate()
    {
        return $this->writtenReasonDate;
    }

    /**
     * Set the written reason letter date
     *
     * @param \DateTime $writtenReasonLetterDate
     * @return Pi
     */
    public function setWrittenReasonLetterDate($writtenReasonLetterDate)
    {
        $this->writtenReasonLetterDate = $writtenReasonLetterDate;

        return $this;
    }

    /**
     * Get the written reason letter date
     *
     * @return \DateTime
     */
    public function getWrittenReasonLetterDate()
    {
        return $this->writtenReasonLetterDate;
    }

    /**
     * Set the pi hearing
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piHearings
     * @return Pi
     */
    public function setPiHearings($piHearings)
    {
        $this->piHearings = $piHearings;

        return $this;
    }

    /**
     * Get the pi hearings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPiHearings()
    {
        return $this->piHearings;
    }

    /**
     * Add a pi hearings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piHearings
     * @return Pi
     */
    public function addPiHearings($piHearings)
    {
        if ($piHearings instanceof ArrayCollection) {
            $this->piHearings = new ArrayCollection(
                array_merge(
                    $this->piHearings->toArray(),
                    $piHearings->toArray()
                )
            );
        } elseif (!$this->piHearings->contains($piHearings)) {
            $this->piHearings->add($piHearings);
        }

        return $this;
    }

    /**
     * Remove a pi hearings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piHearings
     * @return Pi
     */
    public function removePiHearings($piHearings)
    {
        if ($this->piHearings->contains($piHearings)) {
            $this->piHearings->removeElement($piHearings);
        }

        return $this;
    }

    /**
     * Set the publication link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Pi
     */
    public function setPublicationLinks($publicationLinks)
    {
        $this->publicationLinks = $publicationLinks;

        return $this;
    }

    /**
     * Get the publication links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPublicationLinks()
    {
        return $this->publicationLinks;
    }

    /**
     * Add a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Pi
     */
    public function addPublicationLinks($publicationLinks)
    {
        if ($publicationLinks instanceof ArrayCollection) {
            $this->publicationLinks = new ArrayCollection(
                array_merge(
                    $this->publicationLinks->toArray(),
                    $publicationLinks->toArray()
                )
            );
        } elseif (!$this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->add($publicationLinks);
        }

        return $this;
    }

    /**
     * Remove a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Pi
     */
    public function removePublicationLinks($publicationLinks)
    {
        if ($this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->removeElement($publicationLinks);
        }

        return $this;
    }

    /**
     * Set the sla target date
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $slaTargetDates
     * @return Pi
     */
    public function setSlaTargetDates($slaTargetDates)
    {
        $this->slaTargetDates = $slaTargetDates;

        return $this;
    }

    /**
     * Get the sla target dates
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSlaTargetDates()
    {
        return $this->slaTargetDates;
    }

    /**
     * Add a sla target dates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $slaTargetDates
     * @return Pi
     */
    public function addSlaTargetDates($slaTargetDates)
    {
        if ($slaTargetDates instanceof ArrayCollection) {
            $this->slaTargetDates = new ArrayCollection(
                array_merge(
                    $this->slaTargetDates->toArray(),
                    $slaTargetDates->toArray()
                )
            );
        } elseif (!$this->slaTargetDates->contains($slaTargetDates)) {
            $this->slaTargetDates->add($slaTargetDates);
        }

        return $this;
    }

    /**
     * Remove a sla target dates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $slaTargetDates
     * @return Pi
     */
    public function removeSlaTargetDates($slaTargetDates)
    {
        if ($this->slaTargetDates->contains($slaTargetDates)) {
            $this->slaTargetDates->removeElement($slaTargetDates);
        }

        return $this;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
