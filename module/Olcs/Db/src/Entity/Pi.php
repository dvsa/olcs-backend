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
 *        @ORM\Index(name="fk_pi_detail_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_pi_detail_ref_data2_idx", columns={"pi_status"}),
 *        @ORM\Index(name="fk_pi_detail_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_detail_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Pi implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\AgreedDateField,
        Traits\DecisionDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
    protected $isCancelled = 0;

    /**
     * Is adjourned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false)
     */
    protected $isAdjourned = 0;

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
    protected $licenceRevokedAtPi = 0;

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
     * Pi hearing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PiHearing", mappedBy="pi")
     */
    protected $piHearings;

    /**
     * Pi reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PiReason", mappedBy="pi")
     */
    protected $piReasons;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->piTypes = new ArrayCollection();
        $this->piHearings = new ArrayCollection();
        $this->piReasons = new ArrayCollection();
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
     * Set the pi reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $piReasons
     * @return Pi
     */
    public function setPiReasons($piReasons)
    {
        $this->piReasons = $piReasons;

        return $this;
    }

    /**
     * Get the pi reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPiReasons()
    {
        return $this->piReasons;
    }
}
