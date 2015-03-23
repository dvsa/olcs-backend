<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SubCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="sub_category",
 *    indexes={
 *        @ORM\Index(name="ix_sub_category_category_id", columns={"category_id"}),
 *        @ORM\Index(name="ix_sub_category_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_sub_category_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SubCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\IsScanField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is doc
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_doc", nullable=false, options={"default": 0})
     */
    protected $isDoc = 0;

    /**
     * Is free text
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_free_text", nullable=false, options={"default": 0})
     */
    protected $isFreeText = 0;

    /**
     * Is task
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_task", nullable=false, options={"default": 0})
     */
    protected $isTask = 0;

    /**
     * Sub category name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sub_category_name", length=64, nullable=false)
     */
    protected $subCategoryName;

    /**
     * Set the is doc
     *
     * @param boolean $isDoc
     * @return SubCategory
     */
    public function setIsDoc($isDoc)
    {
        $this->isDoc = $isDoc;

        return $this;
    }

    /**
     * Get the is doc
     *
     * @return boolean
     */
    public function getIsDoc()
    {
        return $this->isDoc;
    }

    /**
     * Set the is free text
     *
     * @param boolean $isFreeText
     * @return SubCategory
     */
    public function setIsFreeText($isFreeText)
    {
        $this->isFreeText = $isFreeText;

        return $this;
    }

    /**
     * Get the is free text
     *
     * @return boolean
     */
    public function getIsFreeText()
    {
        return $this->isFreeText;
    }

    /**
     * Set the is task
     *
     * @param boolean $isTask
     * @return SubCategory
     */
    public function setIsTask($isTask)
    {
        $this->isTask = $isTask;

        return $this;
    }

    /**
     * Get the is task
     *
     * @return boolean
     */
    public function getIsTask()
    {
        return $this->isTask;
    }

    /**
     * Set the sub category name
     *
     * @param string $subCategoryName
     * @return SubCategory
     */
    public function setSubCategoryName($subCategoryName)
    {
        $this->subCategoryName = $subCategoryName;

        return $this;
    }

    /**
     * Get the sub category name
     *
     * @return string
     */
    public function getSubCategoryName()
    {
        return $this->subCategoryName;
    }
}
