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
        Traits\IdIdentity,
        Traits\PiVenueManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\HearingDateField,
        Traits\PiVenueOther255Field,
        Traits\CancelledDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Presided by role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by_role", referencedColumnName="id", nullable=true)
     */
    protected $presidedByRole;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY", inversedBy="piHearings")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Presiding tc other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="presiding_tc_other", length=45, nullable=true)
     */
    protected $presidingTcOther;

    /**
     * Is cancelled
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_cancelled", nullable=false)
     */
    protected $isCancelled = 0;

    /**
     * Cancelled reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="cancelled_reason", length=4000, nullable=true)
     */
    protected $cancelledReason;

    /**
     * Is ajourned
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ajourned", nullable=false)
     */
    protected $isAjourned = 0;

    /**
     * Ajourned reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ajourned_reason", length=4000, nullable=true)
     */
    protected $ajournedReason;

    /**
     * Ajourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="ajourned_date", nullable=true)
     */
    protected $ajournedDate;

    /**
     * Details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details", length=4000, nullable=true)
     */
    protected $details;

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

    /**
     * Set the is cancelled
     *
     * @param boolean $isCancelled
     * @return PiHearing
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
     * Set the is ajourned
     *
     * @param boolean $isAjourned
     * @return PiHearing
     */
    public function setIsAjourned($isAjourned)
    {
        $this->isAjourned = $isAjourned;

        return $this;
    }

    /**
     * Get the is ajourned
     *
     * @return boolean
     */
    public function getIsAjourned()
    {
        return $this->isAjourned;
    }

    /**
     * Set the ajourned reason
     *
     * @param string $ajournedReason
     * @return PiHearing
     */
    public function setAjournedReason($ajournedReason)
    {
        $this->ajournedReason = $ajournedReason;

        return $this;
    }

    /**
     * Get the ajourned reason
     *
     * @return string
     */
    public function getAjournedReason()
    {
        return $this->ajournedReason;
    }

    /**
     * Set the ajourned date
     *
     * @param \DateTime $ajournedDate
     * @return PiHearing
     */
    public function setAjournedDate($ajournedDate)
    {
        $this->ajournedDate = $ajournedDate;

        return $this;
    }

    /**
     * Get the ajourned date
     *
     * @return \DateTime
     */
    public function getAjournedDate()
    {
        return $this->ajournedDate;
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
}
