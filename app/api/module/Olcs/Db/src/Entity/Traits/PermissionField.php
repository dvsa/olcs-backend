<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PermissionField
{
    /**
     * Permission
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="permission", nullable=false)
     */
    protected $permission;

    /**
     * Set the permission
     *
     * @param boolean $permission
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get the permission
     *
     * @return boolean
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
