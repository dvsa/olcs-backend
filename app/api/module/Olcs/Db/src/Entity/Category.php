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
 *        @ORM\Index(name="fk_document_category_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_category_ref_data2_idx", columns={"task_allocation_type"})
 *    }
 * )
 */
class Category implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is doc category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_doc_category", nullable=false, options={"default": 1})
     */
    protected $isDocCategory;

    /**
     * Is scan category
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_scan_category", nullable=false, options={"default": 1})
     */
    protected $isScanCategory;

    /**
     * Is task category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_task_category", nullable=false, options={"default": 1})
     */
    protected $isTaskCategory;

    /**
     * Task allocation type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="task_allocation_type", referencedColumnName="id", nullable=true)
     */
    protected $taskAllocationType;

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
     * Set the is scan category
     *
     * @param boolean $isScanCategory
     * @return Category
     */
    public function setIsScanCategory($isScanCategory)
    {
        $this->isScanCategory = $isScanCategory;

        return $this;
    }

    /**
     * Get the is scan category
     *
     * @return boolean
     */
    public function getIsScanCategory()
    {
        return $this->isScanCategory;
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

    /**
     * Set the task allocation type
     *
     * @param \Olcs\Db\Entity\RefData $taskAllocationType
     * @return Category
     */
    public function setTaskAllocationType($taskAllocationType)
    {
        $this->taskAllocationType = $taskAllocationType;

        return $this;
    }

    /**
     * Get the task allocation type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTaskAllocationType()
    {
        return $this->taskAllocationType;
    }
}
