<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presided by many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait PresidedByManyToOne
{
    /**
     * Presided by
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by", referencedColumnName="id", nullable=true)
     */
    protected $presidedBy;

    /**
     * Set the presided by
     *
     * @param \Olcs\Db\Entity\RefData $presidedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPresidedBy($presidedBy)
    {
        $this->presidedBy = $presidedBy;

        return $this;
    }

    /**
     * Get the presided by
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPresidedBy()
    {
        return $this->presidedBy;
    }
}
