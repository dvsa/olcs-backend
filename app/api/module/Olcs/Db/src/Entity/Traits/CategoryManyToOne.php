<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category many to one trait
 *
 * Auto-Generated (Shared between 5 entities)
 */
trait CategoryManyToOne
{
    /**
     * Category
     *
     * @var \Olcs\Db\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Category", fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;

    /**
     * Set the category
     *
     * @param \Olcs\Db\Entity\Category $category
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
}
