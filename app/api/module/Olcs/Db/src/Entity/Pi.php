<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Pi Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="pi",
 *    indexes={
 *        @ORM\Index(name="IDX_5A88B2F5D34341AA", columns={"agreed_by_tc_role"}),
 *        @ORM\Index(name="IDX_5A88B2F53174D383", columns={"decided_by_tc_role"}),
 *        @ORM\Index(name="IDX_5A88B2F523E72C21", columns={"written_outcome"}),
 *        @ORM\Index(name="IDX_5A88B2F5511CF0D9", columns={"agreed_by_tc_id"}),
 *        @ORM\Index(name="IDX_5A88B2F5DCBFC605", columns={"decided_by_tc_id"}),
 *        @ORM\Index(name="IDX_5A88B2F589EEAF91", columns={"assigned_to"}),
 *        @ORM\Index(name="IDX_5A88B2F5C651310A", columns={"pi_status"}),
 *        @ORM\Index(name="IDX_5A88B2F5CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_5A88B2F5DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5A88B2F565CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Pi implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\AgreedDateField,
        Traits\DecisionDateField,
        Traits\CustomDeletedDateField,
        Traits\Comment4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Agreed by tc role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="agreed_by_tc_role", referencedColumnName="id", nullable=true)
     */
    protected $agreedByTcRole;

    /**
     * Decided by tc
     *
     * @var \Olcs\Db\Entity\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="decided_by_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $decidedByTc;

    /**
     * Pi status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_status", referencedColumnName="id", nullable=false)
     */
    protected $piStatus;

    /**
     * Assigned to
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id", nullable=true)
     */
    protected $assignedTo;

    /**
     * Decided by tc role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="decided_by_tc_role", referencedColumnName="id", nullable=true)
     */
    protected $decidedByTcRole;

    /**
     * Written outcome
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="written_outcome", referencedColumnName="id", nullable=true)
     */
    protected $writtenOutcome;

    /**
     * Agreed by tc
     *
     * @var \Olcs\Db\Entity\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="agreed_by_tc_id", referencedColumnName="id", nullable=true)
     */
    protected $agreedByTc;

    /**
     * Pi type
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="pis", fetch="LAZY")
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
     * Decision
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Decision", inversedBy="pis", fetch="LAZY")
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
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Reason", inversedBy="pis", fetch="LAZY")
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
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witnesses", nullable=true)
     */
    protected $witnesses;

    /**
     * Is cancelled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_cancelled", nullable=false)
     */
    protected $isCancelled;

    /**
     * Is adjourned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false)
     */
    protected $isAdjourned;

    /**
     * Section code text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="section_code_text", length=1024, nullable=true)
     */
    protected $sectionCodeText;

    /**
     * Reschedule datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="reschedule_datetime", nullable=true)
     */
    protected $rescheduleDatetime;

    /**
     * Licence revoked at pi
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="licence_revoked_at_pi", nullable=false)
     */
    protected $licenceRevokedAtPi;

    /**
     * Notification date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="notification_date", nullable=true)
     */
    protected $notificationDate;

    /**
     * Decision notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="decision_notes", length=65535, nullable=true)
     */
    protected $decisionNotes;

    /**
     * Call up letter date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="call_up_letter_date", nullable=true)
     */
    protected $callUpLetterDate;

    /**
     * Brief to tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="brief_to_tc_date", nullable=true)
     */
    protected $briefToTcDate;

    /**
     * Written reason date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="written_reason_date", nullable=true)
     */
    protected $writtenReasonDate;

    /**
     * Decision letter sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_letter_sent_date", nullable=true)
     */
    protected $decisionLetterSentDate;

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
     * Written reason letter date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="written_reason_letter_date", nullable=true)
     */
    protected $writtenReasonLetterDate;

    /**
     * Decision letter sent after written decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_letter_sent_after_written_decision_date", nullable=true)
     */
    protected $decisionLetterSentAfterWrittenDecisionDate;

    /**
     * Pi hearing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PiHearing", mappedBy="pi")
     */
    protected $piHearings;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->piTypes = new ArrayCollection();
        $this->decisions = new ArrayCollection();
        $this->reasons = new ArrayCollection();
        $this->piHearings = new ArrayCollection();
    }

    /**
     * Set the agreed by tc role
     *
     * @param \Olcs\Db\Entity\RefData $agreedByTcRole
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getAgreedByTcRole()
    {
        return $this->agreedByTcRole;
    }

    /**
     * Set the decided by tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $decidedByTc
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
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getDecidedByTc()
    {
        return $this->decidedByTc;
    }

    /**
     * Set the pi status
     *
     * @param \Olcs\Db\Entity\RefData $piStatus
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPiStatus()
    {
        return $this->piStatus;
    }

    /**
     * Set the assigned to
     *
     * @param \Olcs\Db\Entity\User $assignedTo
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
     * @return \Olcs\Db\Entity\User
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set the decided by tc role
     *
     * @param \Olcs\Db\Entity\RefData $decidedByTcRole
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getDecidedByTcRole()
    {
        return $this->decidedByTcRole;
    }

    /**
     * Set the written outcome
     *
     * @param \Olcs\Db\Entity\RefData $writtenOutcome
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWrittenOutcome()
    {
        return $this->writtenOutcome;
    }

    /**
     * Set the agreed by tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $agreedByTc
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
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getAgreedByTc()
    {
        return $this->agreedByTc;
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
     * Set the is adjourned
     *
     * @param string $isAdjourned
     * @return Pi
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return string
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
    }

    /**
     * Set the section code text
     *
     * @param string $sectionCodeText
     * @return Pi
     */
    public function setSectionCodeText($sectionCodeText)
    {
        $this->sectionCodeText = $sectionCodeText;

        return $this;
    }

    /**
     * Get the section code text
     *
     * @return string
     */
    public function getSectionCodeText()
    {
        return $this->sectionCodeText;
    }

    /**
     * Set the reschedule datetime
     *
     * @param \DateTime $rescheduleDatetime
     * @return Pi
     */
    public function setRescheduleDatetime($rescheduleDatetime)
    {
        $this->rescheduleDatetime = $rescheduleDatetime;

        return $this;
    }

    /**
     * Get the reschedule datetime
     *
     * @return \DateTime
     */
    public function getRescheduleDatetime()
    {
        return $this->rescheduleDatetime;
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
     * Set the decision letter sent after written decision date
     *
     * @param \DateTime $input
     * @return Pi
     */
    public function setDecisionLetterSentAfterWrittenDecisionDate($input)
    {
        $this->decisionLetterSentAfterWrittenDecisionDate = $input;

        return $this;
    }

    /**
     * Get the decision letter sent after written decision date
     *
     * @return \DateTime
     */
    public function getDecisionLetterSentAfterWrittenDecisionDate()
    {
        return $this->decisionLetterSentAfterWrittenDecisionDate;
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
}
