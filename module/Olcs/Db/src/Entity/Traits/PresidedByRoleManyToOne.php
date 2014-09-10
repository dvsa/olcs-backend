<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presided by role many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PresidedByRoleManyToOne
{
    /**
     * Presided by role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by_role", referencedColumnName="id", nullable=true)
     */
    protected $presidedByRole;

    /**
     * Set the presided by role
     *
     * @param \Olcs\Db\Entity\RefData $presidedByRole
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPresidedByRole($presidedByRole)
    {
        $this->presidedByRole = $presidedByRole;

        return $this;
    }

    /**
     * Get the presided by role
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPresidedByRole()
    {
        return $this->presidedByRole;
    }
}
