<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is read only field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsReadOnlyField
{
    /**
     * Is read only
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_read_only", nullable=false)
     */
    protected $isReadOnly;

    /**
     * Set the is read only
     *
     * @param boolean $isReadOnly
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return boolean
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }
}
