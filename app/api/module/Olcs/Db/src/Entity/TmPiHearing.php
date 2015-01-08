<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmPiHearing Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_pi_hearing",
 *    indexes={
 *        @ORM\Index(name="fk_tm_pi_hearing_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_ref_data1_idx", columns={"presided_by"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_ref_data2_idx", columns={"reason_id"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_ref_data3_idx", columns={"type_id"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_tm_pi_hearing_pi_venue1_idx", columns={"venue_id"})
 *    }
 * )
 */
class TmPiHearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AgreedDateField,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\PresidingTcManyToOneAlt1,
        Traits\VenueManyToOne,
        Traits\CustomVersionField;

    /**
     * Adjourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="adjourned_date", nullable=true)
     */
    protected $adjournedDate;

    /**
     * Cancelled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="cancelled_date", nullable=true)
     */
    protected $cancelledDate;

    /**
     * Presided by
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="presided_by", referencedColumnName="id", nullable=true)
     */
    protected $presidedBy;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Rescheduled on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="rescheduled_on", nullable=true)
     */
    protected $rescheduledOn;

    /**
     * Scheduled on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="scheduled_on", nullable=true)
     */
    protected $scheduledOn;

    /**
     * Type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witnesses", nullable=false)
     */
    protected $witnesses = 0;

    /**
     * Set the adjourned date
     *
     * @param \DateTime $adjournedDate
     * @return TmPiHearing
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
     * Set the cancelled date
     *
     * @param \DateTime $cancelledDate
     * @return TmPiHearing
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
     * Set the presided by
     *
     * @param \Olcs\Db\Entity\RefData $presidedBy
     * @return TmPiHearing
     */
    public function setPresidedBy($presidedBy)
    {
        $this->presidedBy = $presidedBy;

        return $this;
    }

    /**
     * Get the presided by
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPresidedBy()
    {
        return $this->presidedBy;
    }

    /**
     * Set the reason
     *
     * @param \Olcs\Db\Entity\RefData $reason
     * @return TmPiHearing
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the rescheduled on
     *
     * @param \DateTime $rescheduledOn
     * @return TmPiHearing
     */
    public function setRescheduledOn($rescheduledOn)
    {
        $this->rescheduledOn = $rescheduledOn;

        return $this;
    }

    /**
     * Get the rescheduled on
     *
     * @return \DateTime
     */
    public function getRescheduledOn()
    {
        return $this->rescheduledOn;
    }

    /**
     * Set the scheduled on
     *
     * @param \DateTime $scheduledOn
     * @return TmPiHearing
     */
    public function setScheduledOn($scheduledOn)
    {
        $this->scheduledOn = $scheduledOn;

        return $this;
    }

    /**
     * Get the scheduled on
     *
     * @return \DateTime
     */
    public function getScheduledOn()
    {
        return $this->scheduledOn;
    }

    /**
     * Set the type
     *
     * @param \Olcs\Db\Entity\RefData $type
     * @return TmPiHearing
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the witnesses
     *
     * @param int $witnesses
     * @return TmPiHearing
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
}
