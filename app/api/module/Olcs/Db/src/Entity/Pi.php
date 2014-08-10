<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
        Traits\CustomDeletedDateField,
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_app_new", nullable=false)
     */
    protected $typeAppNew = 0;

    /**
     * Type app var
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_app_var", nullable=false)
     */
    protected $typeAppVar = 0;

    /**
     * Type disciplinary
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_disciplinary", nullable=false)
     */
    protected $typeDisciplinary = 0;

    /**
     * Type env new
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_env_new", nullable=false)
     */
    protected $typeEnvNew = 0;

    /**
     * Type env var
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_env_var", nullable=false)
     */
    protected $typeEnvVar = 0;

    /**
     * Type oc review
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_oc_review", nullable=false)
     */
    protected $typeOcReview = 0;

    /**
     * Type impounding
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_impounding", nullable=false)
     */
    protected $typeImpounding = 0;

    /**
     * Type other
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="type_other", nullable=false)
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_cancelled", nullable=false)
     */
    protected $isCancelled = 0;

    /**
     * Is adjourned
     *
     * @var unknown
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
     * Licence revoked at pi
     *
     * @var unknown
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
     * @ORM\Column(type="text", name="decision_notes", nullable=true)
     */
    protected $decisionNotes;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
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
     * @param unknown $typeAppNew
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
     * @return unknown
     */
    public function getTypeAppNew()
    {
        return $this->typeAppNew;
    }


    /**
     * Set the type app var
     *
     * @param unknown $typeAppVar
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
     * @return unknown
     */
    public function getTypeAppVar()
    {
        return $this->typeAppVar;
    }


    /**
     * Set the type disciplinary
     *
     * @param unknown $typeDisciplinary
     * @return Pi
     */
    public function setTypeDisciplinary($typeDisciplinary)
    {
        $this->typeDisciplinary = $typeDisciplinary;

        return $this;
    }

    /**
     * Get the type disciplinary
     *
     * @return unknown
     */
    public function getTypeDisciplinary()
    {
        return $this->typeDisciplinary;
    }


    /**
     * Set the type env new
     *
     * @param unknown $typeEnvNew
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
     * @return unknown
     */
    public function getTypeEnvNew()
    {
        return $this->typeEnvNew;
    }


    /**
     * Set the type env var
     *
     * @param unknown $typeEnvVar
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
     * @return unknown
     */
    public function getTypeEnvVar()
    {
        return $this->typeEnvVar;
    }


    /**
     * Set the type oc review
     *
     * @param unknown $typeOcReview
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
     * @return unknown
     */
    public function getTypeOcReview()
    {
        return $this->typeOcReview;
    }


    /**
     * Set the type impounding
     *
     * @param unknown $typeImpounding
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
     * @return unknown
     */
    public function getTypeImpounding()
    {
        return $this->typeImpounding;
    }


    /**
     * Set the type other
     *
     * @param unknown $typeOther
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
     * @return unknown
     */
    public function getTypeOther()
    {
        return $this->typeOther;
    }


    /**
     * Set the pi datetime
     *
     * @param \DateTime $piDatetime
     * @return Pi
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
     * @return Pi
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
     * @param unknown $isCancelled
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
     * @return unknown
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }


    /**
     * Set the is adjourned
     *
     * @param unknown $isAdjourned
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
     * @return unknown
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
     * Set the licence revoked at pi
     *
     * @param unknown $licenceRevokedAtPi
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
     * @return unknown
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

}
