<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiDefinitionCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_definition_category")
 */
class PiDefinitionCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Category
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category", length=32, nullable=false)
     */
    protected $category;


    /**
     * Set the category
     *
     * @param string $category
     * @return PiDefinitionCategory
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
