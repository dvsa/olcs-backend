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
    const ROLE_INTERNAL_LIMITED_READ_ONLY = 21;
    const ROLE_INTERNAL_READ_ONLY = 22;
    const ROLE_INTERNAL_CASE_WORKER = 23;
    const ROLE_INTERNAL_ADMIN = 24;
    const ROLE_OPERATOR_ADMIN = 25;
    const ROLE_OPERATOR_USER = 26;
    const ROLE_OPERATOR_TM = 27;
    const ROLE_PARTNER_ADMIN = 29;
    const ROLE_PARTNER_USER = 30;
    const ROLE_LOCAL_AUTHORITY_ADMIN = 31;
    const ROLE_LOCAL_AUTHORITY_USER = 32;

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
