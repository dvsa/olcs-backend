<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Pi Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi",
 *    indexes={
 *        @ORM\Index(name="fk_pi_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_pi_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_pi_ref_data1_idx", columns={"presided_by"}),
 *        @ORM\Index(name="fk_pi_ref_data2_idx", columns={"pi_status"}),
 *        @ORM\Index(name="fk_pi_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Pi implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PresidedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\CaseManyToOne,
        Traits\AgreedDateField,
        Traits\PresidingTcOther45Field,
        Traits\RescheduleDatetimeField,
        Traits\DecisionDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="pi_status", referencedColumnName="id")
     */
    protected $piStatus;

    /**
     * Type app new
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_app_new", nullable=false)
     */
    protected $typeAppNew = 0;

    /**
     * Type app var
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_app_var", nullable=false)
     */
    protected $typeAppVar = 0;

    /**
     * Type discipliniary
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_discipliniary", nullable=false)
     */
    protected $typeDiscipliniary = 0;

    /**
     * Type env new
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_env_new", nullable=false)
     */
    protected $typeEnvNew = 0;

    /**
     * Type env var
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_env_var", nullable=false)
     */
    protected $typeEnvVar = 0;

    /**
     * Type oc review
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_oc_review", nullable=false)
     */
    protected $typeOcReview = 0;

    /**
     * Type impounding
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_impounding", nullable=false)
     */
    protected $typeImpounding = 0;

    /**
     * Type other
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="type_other", nullable=false)
     */
    protected $typeOther = 0;

    /**
     * Pi datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="pi_datetime", nullable=true)
     */
    protected $piDatetime;

    /**
     * Venue
     *
     * @var string
     *
     * @ORM\Column(type="string", name="venue", length=255, nullable=true)
     */
    protected $venue;

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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_cancelled", nullable=false)
     */
    protected $isCancelled = 0;

    /**
     * Is adjourned
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_adjourned", nullable=false)
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
     * Licence revoked at pi
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="licence_revoked_at_pi", nullable=false)
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
     * @ORM\Column(type="text", name="decision_notes", nullable=true)
     */
    protected $decisionNotes;

    /**
     * Set the pi status
     *
     * @param \Olcs\Db\Entity\RefData $piStatus
     * @return \Olcs\Db\Entity\Pi
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
     * @param boolean $typeAppNew
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeAppNew($typeAppNew)
    {
        $this->typeAppNew = $typeAppNew;

        return $this;
    }

    /**
     * Get the type app new
     *
     * @return boolean
     */
    public function getTypeAppNew()
    {
        return $this->typeAppNew;
    }

    /**
     * Set the type app var
     *
     * @param boolean $typeAppVar
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeAppVar($typeAppVar)
    {
        $this->typeAppVar = $typeAppVar;

        return $this;
    }

    /**
     * Get the type app var
     *
     * @return boolean
     */
    public function getTypeAppVar()
    {
        return $this->typeAppVar;
    }

    /**
     * Set the type discipliniary
     *
     * @param boolean $typeDiscipliniary
     * @return \Olcs\Db\Entity\Pi
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
     * @param boolean $typeEnvNew
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeEnvNew($typeEnvNew)
    {
        $this->typeEnvNew = $typeEnvNew;

        return $this;
    }

    /**
     * Get the type env new
     *
     * @return boolean
     */
    public function getTypeEnvNew()
    {
        return $this->typeEnvNew;
    }

    /**
     * Set the type env var
     *
     * @param boolean $typeEnvVar
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeEnvVar($typeEnvVar)
    {
        $this->typeEnvVar = $typeEnvVar;

        return $this;
    }

    /**
     * Get the type env var
     *
     * @return boolean
     */
    public function getTypeEnvVar()
    {
        return $this->typeEnvVar;
    }

    /**
     * Set the type oc review
     *
     * @param boolean $typeOcReview
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeOcReview($typeOcReview)
    {
        $this->typeOcReview = $typeOcReview;

        return $this;
    }

    /**
     * Get the type oc review
     *
     * @return boolean
     */
    public function getTypeOcReview()
    {
        return $this->typeOcReview;
    }

    /**
     * Set the type impounding
     *
     * @param boolean $typeImpounding
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeImpounding($typeImpounding)
    {
        $this->typeImpounding = $typeImpounding;

        return $this;
    }

    /**
     * Get the type impounding
     *
     * @return boolean
     */
    public function getTypeImpounding()
    {
        return $this->typeImpounding;
    }

    /**
     * Set the type other
     *
     * @param boolean $typeOther
     * @return \Olcs\Db\Entity\Pi
     */
    public function setTypeOther($typeOther)
    {
        $this->typeOther = $typeOther;

        return $this;
    }

    /**
     * Get the type other
     *
     * @return boolean
     */
    public function getTypeOther()
    {
        return $this->typeOther;
    }

    /**
     * Set the pi datetime
     *
     * @param \DateTime $piDatetime
     * @return \Olcs\Db\Entity\Pi
     */
    public function setPiDatetime($piDatetime)
    {
        $this->piDatetime = $piDatetime;

        return $this;
    }

    /**
     * Get the pi datetime
     *
     * @return \DateTime
     */
    public function getPiDatetime()
    {
        return $this->piDatetime;
    }

    /**
     * Set the venue
     *
     * @param string $venue
     * @return \Olcs\Db\Entity\Pi
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get the venue
     *
     * @return string
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set the witnesses
     *
     * @param int $witnesses
     * @return \Olcs\Db\Entity\Pi
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
     * @param boolean $isCancelled
     * @return \Olcs\Db\Entity\Pi
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get the is cancelled
     *
     * @return boolean
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set the is adjourned
     *
     * @param boolean $isAdjourned
     * @return \Olcs\Db\Entity\Pi
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return boolean
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
    }

    /**
     * Set the section code text
     *
     * @param string $sectionCodeText
     * @return \Olcs\Db\Entity\Pi
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
     * Set the licence revoked at pi
     *
     * @param boolean $licenceRevokedAtPi
     * @return \Olcs\Db\Entity\Pi
     */
    public function setLicenceRevokedAtPi($licenceRevokedAtPi)
    {
        $this->licenceRevokedAtPi = $licenceRevokedAtPi;

        return $this;
    }

    /**
     * Get the licence revoked at pi
     *
     * @return boolean
     */
    public function getLicenceRevokedAtPi()
    {
        return $this->licenceRevokedAtPi;
    }

    /**
     * Set the notification date
     *
     * @param \DateTime $notificationDate
     * @return \Olcs\Db\Entity\Pi
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
     * @return \Olcs\Db\Entity\Pi
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
}
