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
 *        @ORM\Index(name="fk_document_category_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_document_category_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Category implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255FieldAlt1,
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
    protected $isDocCategory = 1;

    /**
     * Is task category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_task_category", nullable=false)
     */
    protected $isTaskCategory = 1;


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
