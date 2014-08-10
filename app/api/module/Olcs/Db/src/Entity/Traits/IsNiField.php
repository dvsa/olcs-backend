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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_ni", nullable=false)
     */
    protected $isNi;

    /**
     * Set the is ni
     *
     * @param unknown $isNi
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
     * @return unknown
     */
    public function getIsNi()
    {
        return $this->isNi;
    }

}
