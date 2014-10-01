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
 *        @ORM\Index(name="IDX_527EDB25B1CA7F73", columns={"task_sub_category_id"}),
 *        @ORM\Index(name="IDX_527EDB2587CC8891", columns={"irfo_organisation_id"}),
 *        @ORM\Index(name="IDX_527EDB25E43D4745", columns={"assigned_by_user_id"}),
 *        @ORM\Index(name="IDX_527EDB259F55862A", columns={"assigned_to_team_id"}),
 *        @ORM\Index(name="IDX_527EDB2511578D11", columns={"assigned_to_user_id"}),
 *        @ORM\Index(name="IDX_527EDB2565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_527EDB25DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_527EDB2512469DE2", columns={"category_id"}),
 *        @ORM\Index(name="IDX_527EDB25CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_527EDB251F75BD29", columns={"transport_manager_id"}),
 *        @ORM\Index(name="IDX_527EDB2526EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_527EDB253E030ACD", columns={"application_id"}),
 *        @ORM\Index(name="IDX_527EDB255327B2E3", columns={"bus_reg_id"})
 *    }
 * )
 */
class Task implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CategoryManyToOne,
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
     * Assigned to team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_to_team_id", referencedColumnName="id", nullable=true)
     */
    protected $assignedToTeam;

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
     * Irfo organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_organisation_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoOrganisation;

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
    protected $isClosed;

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
    protected $urgent;

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
