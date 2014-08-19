<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiRescheduleDate Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_reschedule_date",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reschedule_dates_pi1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_dates_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_dates_ref_data1_idx", columns={"presided_by"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PiRescheduleDate implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PresidedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\RescheduleDatetimeField,
        Traits\PresidingTcOther45Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Adjournment datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="adjournment_datetime", nullable=true)
     */
    protected $adjournmentDatetime;


    /**
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return PiRescheduleDate
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
     * Set the adjournment datetime
     *
     * @param \DateTime $adjournmentDatetime
     * @return PiRescheduleDate
     */
    public function setAdjournmentDatetime($adjournmentDatetime)
    {
        $this->adjournmentDatetime = $adjournmentDatetime;

        return $this;
    }

    /**
     * Get the adjournment datetime
     *
     * @return \DateTime
     */
    public function getAdjournmentDatetime()
    {
        return $this->adjournmentDatetime;
    }
}
