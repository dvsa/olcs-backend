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
     * @var \Olcs\Db\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * Set the role
     *
     * @param \Olcs\Db\Entity\Role $role
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
     * @return \Olcs\Db\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
