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
 *        @ORM\Index(name="fk_role_has_permission_permission1_idx", columns={"permission_id"}),
 *        @ORM\Index(name="fk_role_has_permission_role1_idx", columns={"role_id"}),
 *        @ORM\Index(name="fk_role_permission_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_role_permission_user2_idx", columns={"last_modified_by"})
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
