<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiHearing Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_hearing",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reschedule_dates_pi_detail1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_dates_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_pi_hearing_ref_data1_idx", columns={"presided_by_role"}),
 *        @ORM\Index(name="fk_pi_hearing_pi_venue1_idx", columns={"pi_venue_id"})
 *    }
 * )
 */
class PiHearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\HearingDateField,
        Traits\IdIdentity,
        Traits\IsCancelledField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\PiVenueManyToOne,
        Traits\PiVenueOther255Field,
        Traits\PresidingTcManyToOne,
        Traits\CustomVersionField,
        Traits\WitnessesField;

    /**
     * Adjourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="adjourned_date", nullable=true)
     */
    protected $adjournedDate;

    /**
     * Adjourned reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="adjourned_reason", length=4000, nullable=true)
     */
    protected $adjournedReason;

    /**
     * Cancelled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancelled_date", nullable=true)
     */
    protected $cancelledDate;

    /**
     * Cancelled reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="cancelled_reason", length=4000, nullable=true)
     */
    protected $cancelledReason;

    /**
     * Details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details", length=4000, nullable=true)
     */
    protected $details;

    /**
     * Is adjourned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false, options={"default": 0})
     */
    protected $isAdjourned = 0;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", inversedBy="piHearings")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Presided by role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="presided_by_role", referencedColumnName="id", nullable=true)
     */
    protected $presidedByRole;

    /**
     * Presiding tc other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="presiding_tc_other", length=45, nullable=true)
     */
    protected $presidingTcOther;

    /**
     * Set the adjourned date
     *
     * @param \DateTime $adjournedDate
     * @return PiHearing
     */
    public function setAdjournedDate($adjournedDate)
    {
        $this->adjournedDate = $adjournedDate;

        return $this;
    }

    /**
     * Get the adjourned date
     *
     * @return \DateTime
     */
    public function getAdjournedDate()
    {
        return $this->adjournedDate;
    }

    /**
     * Set the adjourned reason
     *
     * @param string $adjournedReason
     * @return PiHearing
     */
    public function setAdjournedReason($adjournedReason)
    {
        $this->adjournedReason = $adjournedReason;

        return $this;
    }

    /**
     * Get the adjourned reason
     *
     * @return string
     */
    public function getAdjournedReason()
    {
        return $this->adjournedReason;
    }

    /**
     * Set the cancelled date
     *
     * @param \DateTime $cancelledDate
     * @return PiHearing
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get the cancelled date
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Set the cancelled reason
     *
     * @param string $cancelledReason
     * @return PiHearing
     */
    public function setCancelledReason($cancelledReason)
    {
        $this->cancelledReason = $cancelledReason;

        return $this;
    }

    /**
     * Get the cancelled reason
     *
     * @return string
     */
    public function getCancelledReason()
    {
        return $this->cancelledReason;
    }

    /**
     * Set the details
     *
     * @param string $details
     * @return PiHearing
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the is adjourned
     *
     * @param string $isAdjourned
     * @return PiHearing
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
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return PiHearing
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Olcs\Db\Entity\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * Set the presided by role
     *
     * @param \Olcs\Db\Entity\RefData $presidedByRole
     * @return PiHearing
     */
    public function setPresidedByRole($presidedByRole)
    {
        $this->presidedByRole = $presidedByRole;

        return $this;
    }

    /**
     * Get the presided by role
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPresidedByRole()
    {
        return $this->presidedByRole;
    }

    /**
     * Set the presiding tc other
     *
     * @param string $presidingTcOther
     * @return PiHearing
     */
    public function setPresidingTcOther($presidingTcOther)
    {
        $this->presidingTcOther = $presidingTcOther;

        return $this;
    }

    /**
     * Get the presiding tc other
     *
     * @return string
     */
    public function getPresidingTcOther()
    {
        return $this->presidingTcOther;
    }
}
