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
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="deleted", nullable=true)
     */
    protected $deleted = 0;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * Set the deleted
     *
     * @param unknown $deleted
     * @return PresidingTc
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the deleted
     *
     * @return unknown
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

}
