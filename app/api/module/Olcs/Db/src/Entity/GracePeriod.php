<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * GracePeriod Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="grace_period",
 *    indexes={
 *        @ORM\Index(name="ix_grace_period_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_grace_period_assigned_to_user_id", columns={"assigned_to_user_id"}),
 *        @ORM\Index(name="ix_grace_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_grace_period_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_grace_period_period_type", columns={"period_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_grace_period_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class GracePeriod implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Assigned to user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="assigned_to_user_id", referencedColumnName="id", nullable=false)
     */
    protected $assignedToUser;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date", nullable=false)
     */
    protected $endDate;

    /**
     * Grace period no
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="grace_period_no", nullable=false, options={"default": 1})
     */
    protected $gracePeriodNo = 1;

    /**
     * Is active
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_active", nullable=false, options={"default": 0})
     */
    protected $isActive = 0;

    /**
     * Period type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="period_type", referencedColumnName="id", nullable=false)
     */
    protected $periodType;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date", nullable=false)
     */
    protected $startDate;

    /**
     * Set the assigned to user
     *
     * @param \Olcs\Db\Entity\User $assignedToUser
     * @return GracePeriod
     */
    public function setAssignedToUser($assignedToUser)
    {
        $this->assignedToUser = $assignedToUser;

        return $this;
    }

    /**
     * Get the assigned to user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getAssignedToUser()
    {
        return $this->assignedToUser;
    }

    /**
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return GracePeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the grace period no
     *
     * @param boolean $gracePeriodNo
     * @return GracePeriod
     */
    public function setGracePeriodNo($gracePeriodNo)
    {
        $this->gracePeriodNo = $gracePeriodNo;

        return $this;
    }

    /**
     * Get the grace period no
     *
     * @return boolean
     */
    public function getGracePeriodNo()
    {
        return $this->gracePeriodNo;
    }

    /**
     * Set the is active
     *
     * @param boolean $isActive
     * @return GracePeriod
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the is active
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the period type
     *
     * @param \Olcs\Db\Entity\RefData $periodType
     * @return GracePeriod
     */
    public function setPeriodType($periodType)
    {
        $this->periodType = $periodType;

        return $this;
    }

    /**
     * Get the period type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPeriodType()
    {
        return $this->periodType;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return GracePeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
