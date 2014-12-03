<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is ni field alt1 trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsNiFieldAlt1
{
    /**
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni", nullable=false)
     */
    protected $isNi;

    /**
     * Set the is ni
     *
     * @param string $isNi
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
     * @return string
     */
    public function getIsNi()
    {
        return $this->isNi;
    }
}
