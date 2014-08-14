<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TmGracePeriod Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tm_grace_period",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_grace_period_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_transport_manager_grace_period_user1_idx", columns={"assigned_to_user_id"}),
 *        @ORM\Index(name="fk_transport_manager_grace_period_user2_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_grace_period_user3_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmGracePeriod implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Assigned to user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to_user_id", referencedColumnName="id", nullable=false)
     */
    protected $assignedToUser;

    /**
     * Is active
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_active", nullable=false)
     */
    protected $isActive = 0;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date", nullable=false)
     */
    protected $startDate;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date", nullable=false)
     */
    protected $endDate;

    /**
     * Action date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="action_date", nullable=false)
     */
    protected $actionDate;

    /**
     * Grace period no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="grace_period_no", nullable=false)
     */
    protected $gracePeriodNo = 1;


    /**
     * Set the assigned to user
     *
     * @param \Olcs\Db\Entity\User $assignedToUser
     * @return TmGracePeriod
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
     * Set the is active
     *
     * @param string $isActive
     * @return TmGracePeriod
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the is active
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return TmGracePeriod
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

    /**
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return TmGracePeriod
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
     * Set the action date
     *
     * @param \DateTime $actionDate
     * @return TmGracePeriod
     */
    public function setActionDate($actionDate)
    {
        $this->actionDate = $actionDate;

        return $this;
    }

    /**
     * Get the action date
     *
     * @return \DateTime
     */
    public function getActionDate()
    {
        return $this->actionDate;
    }

    /**
     * Set the grace period no
     *
     * @param int $gracePeriodNo
     * @return TmGracePeriod
     */
    public function setGracePeriodNo($gracePeriodNo)
    {
        $this->gracePeriodNo = $gracePeriodNo;

        return $this;
    }

    /**
     * Get the grace period no
     *
     * @return int
     */
    public function getGracePeriodNo()
    {
        return $this->gracePeriodNo;
    }
}
