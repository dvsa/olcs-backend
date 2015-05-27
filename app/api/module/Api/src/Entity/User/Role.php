<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Rbac\Role\RoleInterface;

/**
 * Role Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="role",
 *    indexes={
 *        @ORM\Index(name="ix_role_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_role_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Role extends AbstractRole implements RoleInterface
{
    /**
     * Get the name of the role.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getRole();
    }

    /**
     * Checks if a permission exists for this role (it does not check child roles)
     *
     * @param  mixed $permission
     * @return bool
     */
    public function hasPermission($permission)
    {

        /** @var RolePermission $rolePermission */
        foreach ($this->getRolePermissions() as $rolePermission) {
            if ($rolePermission->getPermission()->getName() === $permission) {
                return true;
            }
        }

        return false;
    }
}
