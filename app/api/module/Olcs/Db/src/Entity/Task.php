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
 *        @ORM\Index(name="fk_task_task_sub_category1_idx", columns={"task_sub_category_id"}),
 *        @ORM\Index(name="fk_task_cases1_idx", columns={"case_id"})
 *    }
 * )
 */
class Task implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CategoryManyToOne,
        Traits\CaseManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\LicenceManyToOneAlt1,
        Traits\ApplicationManyToOneAlt1,
        Traits\BusRegManyToOneAlt1,
        Traits\Description4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Task sub category
     *
     * @var \Olcs\Db\Entity\TaskSubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TaskSubCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="task_sub_category_id", referencedColumnName="id", nullable=false)
     */
    protected $taskSubCategory;

    /**
     * Irfo organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_organisation_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoOrganisation;

    /**
     * Assigned by user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_by_user_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedByUser;

    /**
     * Assigned to team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to_team_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedToTeam;

    /**
     * Assigned to user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to_user_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedToUser;

    /**
     * Is closed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_closed", nullable=false)
     */
    protected $isClosed = 0;

    /**
     * Action date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="action_date", nullable=true)
     */
    protected $actionDate;

    /**
     * Urgent
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="urgent", nullable=false)
     */
    protected $urgent = 0;

    /**
     * Set the task sub category
     *
     * @param \Olcs\Db\Entity\TaskSubCategory $taskSubCategory
     * @return Task
     */
    public function setTaskSubCategory($taskSubCategory)
    {
        $this->taskSubCategory = $taskSubCategory;

        return $this;
    }

    /**
     * Get the task sub category
     *
     * @return \Olcs\Db\Entity\TaskSubCategory
     */
    public function getTaskSubCategory()
    {
        return $this->taskSubCategory;
    }

    /**
     * Set the irfo organisation
     *
     * @param \Olcs\Db\Entity\Organisation $irfoOrganisation
     * @return Task
     */
    public function setIrfoOrganisation($irfoOrganisation)
    {
        $this->irfoOrganisation = $irfoOrganisation;

        return $this;
    }

    /**
     * Get the irfo organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getIrfoOrganisation()
    {
        return $this->irfoOrganisation;
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
