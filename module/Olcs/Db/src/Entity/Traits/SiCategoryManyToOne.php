<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Si category many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait SiCategoryManyToOne
{
    /**
     * Si category
     *
     * @var \Olcs\Db\Entity\SiCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiCategory")
     * @ORM\JoinColumn(name="si_category_id", referencedColumnName="id", nullable=false)
     */
    protected $siCategory;

    /**
     * Set the si category
     *
     * @param \Olcs\Db\Entity\SiCategory $siCategory
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setSiCategory($siCategory)
    {
        $this->siCategory = $siCategory;

        return $this;
    }

    /**
     * Get the si category
     *
     * @return \Olcs\Db\Entity\SiCategory
     */
    public function getSiCategory()
    {
        return $this->siCategory;
    }
}
