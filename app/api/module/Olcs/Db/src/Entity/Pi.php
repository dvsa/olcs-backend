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
 *        @ORM\Index(name="fk_pi_detail_ref_data1_idx", columns={"presided_by"}),
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
        Traits\CreatedByManyToOne,
        Traits\PresidedByManyToOne,
        Traits\CaseManyToOneAlt1,
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
     * Type app new
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_app_new", nullable=false)
     */
    protected $typeAppNew = 0;

    /**
     * Type app var
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_app_var", nullable=false)
     */
    protected $typeAppVar = 0;

    /**
     * Type discipliniary
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="type_discipliniary", nullable=false)
     */
    protected $typeDiscipliniary = 0;

    /**
     * Type env new
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_env_new", nullable=false)
     */
    protected $typeEnvNew = 0;

    /**
     * Type env var
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_env_var", nullable=false)
     */
    protected $typeEnvVar = 0;

    /**
     * Type oc review
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_oc_review", nullable=false)
     */
    protected $typeOcReview = 0;

    /**
     * Type impounding
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_impounding", nullable=false)
     */
    protected $typeImpounding = 0;

    /**
     * Type other
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="type_other", nullable=false)
     */
    protected $typeOther = 0;

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
     * Set the type app new
     *
     * @param string $typeAppNew
     * @return Pi
     */
    public function setTypeAppNew($typeAppNew)
    {
        $this->typeAppNew = $typeAppNew;

        return $this;
    }

    /**
     * Get the type app new
     *
     * @return string
     */
    public function getTypeAppNew()
    {
        return $this->typeAppNew;
    }

    /**
     * Set the type app var
     *
     * @param string $typeAppVar
     * @return Pi
     */
    public function setTypeAppVar($typeAppVar)
    {
        $this->typeAppVar = $typeAppVar;

        return $this;
    }

    /**
     * Get the type app var
     *
     * @return string
     */
    public function getTypeAppVar()
    {
        return $this->typeAppVar;
    }

    /**
     * Set the type discipliniary
     *
     * @param boolean $typeDiscipliniary
     * @return Pi
     */
    public function setTypeDiscipliniary($typeDiscipliniary)
    {
        $this->typeDiscipliniary = $typeDiscipliniary;

        return $this;
    }

    /**
     * Get the type discipliniary
     *
     * @return boolean
     */
    public function getTypeDiscipliniary()
    {
        return $this->typeDiscipliniary;
    }

    /**
     * Set the type env new
     *
     * @param string $typeEnvNew
     * @return Pi
     */
    public function setTypeEnvNew($typeEnvNew)
    {
        $this->typeEnvNew = $typeEnvNew;

        return $this;
    }

    /**
     * Get the type env new
     *
     * @return string
     */
    public function getTypeEnvNew()
    {
        return $this->typeEnvNew;
    }

    /**
     * Set the type env var
     *
     * @param string $typeEnvVar
     * @return Pi
     */
    public function setTypeEnvVar($typeEnvVar)
    {
        $this->typeEnvVar = $typeEnvVar;

        return $this;
    }

    /**
     * Get the type env var
     *
     * @return string
     */
    public function getTypeEnvVar()
    {
        return $this->typeEnvVar;
    }

    /**
     * Set the type oc review
     *
     * @param string $typeOcReview
     * @return Pi
     */
    public function setTypeOcReview($typeOcReview)
    {
        $this->typeOcReview = $typeOcReview;

        return $this;
    }

    /**
     * Get the type oc review
     *
     * @return string
     */
    public function getTypeOcReview()
    {
        return $this->typeOcReview;
    }

    /**
     * Set the type impounding
     *
     * @param string $typeImpounding
     * @return Pi
     */
    public function setTypeImpounding($typeImpounding)
    {
        $this->typeImpounding = $typeImpounding;

        return $this;
    }

    /**
     * Get the type impounding
     *
     * @return string
     */
    public function getTypeImpounding()
    {
        return $this->typeImpounding;
    }

    /**
     * Set the type other
     *
     * @param string $typeOther
     * @return Pi
     */
    public function setTypeOther($typeOther)
    {
        $this->typeOther = $typeOther;

        return $this;
    }

    /**
     * Get the type other
     *
     * @return string
     */
    public function getTypeOther()
    {
        return $this->typeOther;
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
