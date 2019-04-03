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
    const ROLE_SYSTEM_ADMIN = 'system-admin';
    const ROLE_INTERNAL_LIMITED_READ_ONLY = 'internal-limited-read-only';
    const ROLE_INTERNAL_READ_ONLY = 'internal-read-only';
    const ROLE_INTERNAL_CASE_WORKER = 'internal-case-worker';
    const ROLE_INTERNAL_ADMIN = 'internal-admin';
    const ROLE_OPERATOR_ADMIN = 'operator-admin';
    const ROLE_OPERATOR_USER = 'operator-user';
    const ROLE_OPERATOR_TM = 'operator-tm';
    const ROLE_PARTNER_ADMIN = 'partner-admin';
    const ROLE_PARTNER_USER = 'partner-user';
    const ROLE_LOCAL_AUTHORITY_ADMIN = 'local-authority-admin';
    const ROLE_LOCAL_AUTHORITY_USER = 'local-authority-user';
    const ROLE_ANON = 'anon';


    public static function anon()
    {
        $role = new static();
        $role->setId(self::ROLE_ANON);
        $role->setRole(self::ROLE_ANON);

        return $role;
    }

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
