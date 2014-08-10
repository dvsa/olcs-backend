<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is deleted field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsDeletedField
{
    /**
     * Is deleted
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_deleted", nullable=false)
     */
    protected $isDeleted = 0;

    /**
     * Set the is deleted
     *
     * @param unknown $isDeleted
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get the is deleted
     *
     * @return unknown
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

}
