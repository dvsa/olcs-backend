<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * RolePermission Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="role_permission",
 *    indexes={
 *        @ORM\Index(name="ix_role_permission_permission_id", columns={"permission_id"}),
 *        @ORM\Index(name="ix_role_permission_role_id", columns={"role_id"}),
 *        @ORM\Index(name="ix_role_permission_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_role_permission_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class RolePermission implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Permission
     *
     * @var \Olcs\Db\Entity\Permission
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Permission")
     * @ORM\JoinColumn(name="permission_id", referencedColumnName="id", nullable=false)
     */
    protected $permission;

    /**
     * Role
     *
     * @var \Olcs\Db\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Role", inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * Set the permission
     *
     * @param \Olcs\Db\Entity\Permission $permission
     * @return RolePermission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get the permission
     *
     * @return \Olcs\Db\Entity\Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set the role
     *
     * @param \Olcs\Db\Entity\Role $role
     * @return RolePermission
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
