<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait RoleManyToOne
{
    /**
     * Role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=true)
     */
    protected $role;

    /**
     * Set the role
     *
     * @param \Olcs\Db\Entity\RefData $role
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the role
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRole()
    {
        return $this->role;
    }
}
