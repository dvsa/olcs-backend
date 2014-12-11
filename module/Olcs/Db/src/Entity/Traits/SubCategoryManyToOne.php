<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sub category many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait SubCategoryManyToOne
{
    /**
     * Sub category
     *
     * @var \Olcs\Db\Entity\SubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SubCategory")
     * @ORM\JoinColumn(name="sub_category_id", referencedColumnName="id", nullable=false)
     */
    protected $subCategory;

    /**
     * Set the sub category
     *
     * @param \Olcs\Db\Entity\SubCategory $subCategory
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * Get the sub category
     *
     * @return \Olcs\Db\Entity\SubCategory
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }
}
