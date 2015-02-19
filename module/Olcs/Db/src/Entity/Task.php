<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Task Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task",
 *    indexes={
 *        @ORM\Index(name="fk_task_user1_idx", columns={"assigned_to_user_id"}),
 *        @ORM\Index(name="fk_task_team1_idx", columns={"assigned_to_team_id"}),
 *        @ORM\Index(name="fk_task_user2_idx", columns={"assigned_by_user_id"}),
 *        @ORM\Index(name="fk_task_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_task_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_task_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_task_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_task_organisation1_idx", columns={"irfo_organisation_id"}),
 *        @ORM\Index(name="fk_task_user3_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_task_user4_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_task_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_task_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_task_task_sub_category1_idx", columns={"sub_category_id"})
 *    }
 * )
 */
class Task implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\CaseManyToOneAlt1,
        Traits\CategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255FieldAlt1,
        Traits\IdIdentity,
        Traits\IrfoOrganisationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOneAlt1,
        Traits\SubCategoryManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;

    /**
     * Action date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="action_date", nullable=true)
     */
    protected $actionDate;

    /**
     * Assigned by user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="assigned_by_user_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedByUser;

    /**
     * Assigned to team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team")
     * @ORM\JoinColumn(name="assigned_to_team_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedToTeam;

    /**
     * Assigned to user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="assigned_to_user_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedToUser;

    /**
     * Is closed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_closed", nullable=false, options={"default": 0})
     */
    protected $isClosed;

    /**
     * Urgent
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="urgent", nullable=false, options={"default": 0})
     */
    protected $urgent;

    /**
     * Set the action date
     *
     * @param \DateTime $actionDate
     * @return Task
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
     * Set the assigned by user
     *
     * @param \Olcs\Db\Entity\User $assignedByUser
     * @return Task
     */
    public function setAssignedByUser($assignedByUser)
    {
        $this->assignedByUser = $assignedByUser;

        return $this;
    }

    /**
     * Get the assigned by user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getAssignedByUser()
    {
        return $this->assignedByUser;
    }

    /**
     * Set the assigned to team
     *
     * @param \Olcs\Db\Entity\Team $assignedToTeam
     * @return Task
     */
    public function setAssignedToTeam($assignedToTeam)
    {
        $this->assignedToTeam = $assignedToTeam;

        return $this;
    }

    /**
     * Get the assigned to team
     *
     * @return \Olcs\Db\Entity\Team
     */
    public function getAssignedToTeam()
    {
        return $this->assignedToTeam;
    }

    /**
     * Set the assigned to user
     *
     * @param \Olcs\Db\Entity\User $assignedToUser
     * @return Task
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
     * Set the is closed
     *
     * @param string $isClosed
     * @return Task
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    /**
     * Get the is closed
     *
     * @return string
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set the urgent
     *
     * @param string $urgent
     * @return Task
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * Get the urgent
     *
     * @return string
     */
    public function getUrgent()
    {
        return $this->urgent;
    }
}
