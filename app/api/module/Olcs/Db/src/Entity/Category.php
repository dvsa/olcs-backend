<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Category Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="category",
 *    indexes={
 *        @ORM\Index(name="IDX_64C19C165CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_64C19C1DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class Category implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is doc category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_doc_category", nullable=false)
     */
    protected $isDocCategory;

    /**
     * Is task category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_task_category", nullable=false)
     */
    protected $isTaskCategory;

    /**
     * Set the is doc category
     *
     * @param string $isDocCategory
     * @return Category
     */
    public function setIsDocCategory($isDocCategory)
    {
        $this->isDocCategory = $isDocCategory;

        return $this;
    }

    /**
     * Get the is doc category
     *
     * @return string
     */
    public function getIsDocCategory()
    {
        return $this->isDocCategory;
    }

    /**
     * Set the is task category
     *
     * @param string $isTaskCategory
     * @return Category
     */
    public function setIsTaskCategory($isTaskCategory)
    {
        $this->isTaskCategory = $isTaskCategory;

        return $this;
    }

    /**
     * Get the is task category
     *
     * @return string
     */
    public function getIsTaskCategory()
    {
        return $this->isTaskCategory;
    }
}
