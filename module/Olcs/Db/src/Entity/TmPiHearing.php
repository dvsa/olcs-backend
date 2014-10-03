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
 *        @ORM\Index(name="IDX_48A7AF65C54C8C93", columns={"type_id"}),
 *        @ORM\Index(name="IDX_48A7AF6559BB1592", columns={"reason_id"}),
 *        @ORM\Index(name="IDX_48A7AF65221D9101", columns={"presided_by"}),
 *        @ORM\Index(name="IDX_48A7AF65CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_48A7AF65DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_48A7AF6565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_48A7AF6540A73EBA", columns={"venue_id"}),
 *        @ORM\Index(name="IDX_48A7AF6553BAD7A2", columns={"presiding_tc_id"})
 *    }
 * )
 */
class TmPiHearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CaseManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\VenueManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\AgreedDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Presided by
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by", referencedColumnName="id", nullable=true)
     */
    protected $presidedBy;

    /**
     * Witnesses
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witnesses", nullable=false)
     */
    protected $witnesses;

    /**
     * Adjourned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="adjourned_date", nullable=true)
     */
    protected $adjournedDate;

    /**
     * Cancelled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancelled_date", nullable=true)
     */
    protected $cancelledDate;

    /**
     * Scheduled on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="scheduled_on", nullable=true)
     */
    protected $scheduledOn;

    /**
     * Rescheduled on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="rescheduled_on", nullable=true)
     */
    protected $rescheduledOn;

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
}
