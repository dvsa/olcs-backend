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
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="deleted", nullable=true)
     */
    protected $deleted;

    /**
     * Set the deleted
     *
     * @param string $deleted
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
     * @return string
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
