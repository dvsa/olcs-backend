<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TaskAllocationRule Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task_allocation_rule",
 *    indexes={
 *        @ORM\Index(name="ix_task_allocation_rule_category_id", columns={"category_id"}),
 *        @ORM\Index(name="ix_task_allocation_rule_team_id", columns={"team_id"}),
 *        @ORM\Index(name="ix_task_allocation_rule_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_task_allocation_rule_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_task_allocation_rule_traffic_area_id", columns={"traffic_area_id"})
 *    }
 * )
 */
class TaskAllocationRule implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\GoodsOrPsvManyToOne,
        Traits\IdIdentity,
        Traits\TeamManyToOne,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\UserManyToOne;

    /**
     * Category
     *
     * @var \Olcs\Db\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Category")
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
     * Set the category
     *
     * @param \Olcs\Db\Entity\Category $category
     * @return TaskAllocationRule
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
     * @return TaskAllocationRule
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
