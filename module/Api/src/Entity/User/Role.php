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
    const ROLE_INTERNAL_LIMITED_READ_ONLY = 1;
    const ROLE_INTERNAL_READ_ONLY = 2;
    const ROLE_INTERNAL_CASE_WORKER = 3;
    const ROLE_INTERNAL_ADMIN = 4;
    const ROLE_OPERATOR_ADMIN = 5;
    const ROLE_OPERATOR_USER = 6;
    const ROLE_OPERATOR_TM = 7;
    const ROLE_OPERATOR_EBSR = 8;
    const ROLE_PARTNER_ADMIN = 9;
    const ROLE_PARTNER_USER = 10;
    const ROLE_LOCAL_AUTHORITY_ADMIN = 11;
    const ROLE_LOCAL_AUTHORITY_USER = 12;

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
