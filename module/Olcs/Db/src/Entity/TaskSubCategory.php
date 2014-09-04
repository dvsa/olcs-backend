<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TaskSubCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task_sub_category",
 *    indexes={
 *        @ORM\Index(name="fk_task_sub_category_category1_idx", columns={"category_id"})
 *    }
 * )
 */
class TaskSubCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CategoryManyToOne,
        Traits\Description45Field,
        Traits\Name45Field;

    /**
     * Is freetext description
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_freetext_description", nullable=false)
     */
    protected $isFreetextDescription = 0;

    /**
     * Set the is freetext description
     *
     * @param string $isFreetextDescription
     * @return TaskSubCategory
     */
    public function setIsFreetextDescription($isFreetextDescription)
    {
        $this->isFreetextDescription = $isFreetextDescription;

        return $this;
    }

    /**
     * Get the is freetext description
     *
     * @return string
     */
    public function getIsFreetextDescription()
    {
        return $this->isFreetextDescription;
    }

}
