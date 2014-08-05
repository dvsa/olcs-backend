<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PresidingTc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="presiding_tc")
 */
class PresidingTc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Name70Field;

    /**
     * Deleted
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="deleted", nullable=true)
     */
    protected $deleted = 0;

    /**
     * Set the deleted
     *
     * @param boolean $deleted
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
