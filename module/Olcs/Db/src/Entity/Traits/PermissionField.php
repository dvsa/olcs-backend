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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="permission", nullable=false)
     */
    protected $permission;

    /**
     * Set the permission
     *
     * @param unknown $permission
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
     * @return unknown
     */
    public function getPermission()
    {
        return $this->permission;
    }

}
