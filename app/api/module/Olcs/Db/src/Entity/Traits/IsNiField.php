<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is ni field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsNiField
{
    /**
     * Is ni
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ni", nullable=false)
     */
    protected $isNi;

    /**
     * Set the is ni
     *
     * @param boolean $isNi
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return boolean
     */
    public function getIsNi()
    {
        return $this->isNi;
    }
}
