<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TaskAllocationRules Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task_allocation_rules",
 *    indexes={
 *        @ORM\Index(name="fk_task_allocation_rules_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_task_allocation_rules_team1_idx", columns={"team_id"}),
 *        @ORM\Index(name="fk_task_allocation_rules_user1_idx", columns={"user_id"}),
 *        @ORM\Index(name="fk_task_allocation_rules_ref_data1_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_task_allocation_rules_traffic_area1_idx", columns={"traffic_area_id"})
 *    }
 * )
 */
class TaskAllocationRules implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\TeamManyToOne,
        Traits\GoodsOrPsvManyToOneAlt1;

    /**
     * User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * Category
     *
     * @var \Olcs\Db\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Category", fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    protected $category;

    /**
     * Is mlh
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_mlh", nullable=true)
     */
    protected $isMlh;

    /**
     * Set the user
     *
     * @param \Olcs\Db\Entity\User $user
     * @return TaskAllocationRules
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the category
     *
     * @param \Olcs\Db\Entity\Category $category
     * @return TaskAllocationRules
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the category
     *
     * @return \Olcs\Db\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the is mlh
     *
     * @param boolean $isMlh
     * @return TaskAllocationRules
     */
    public function setIsMlh($isMlh)
    {
        $this->isMlh = $isMlh;

        return $this;
    }

    /**
     * Get the is mlh
     *
     * @return boolean
     */
    public function getIsMlh()
    {
        return $this->isMlh;
    }
}
