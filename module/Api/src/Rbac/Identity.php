<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Identity\IdentityInterface;
use Dvsa\Olcs\Api\Entity\User\UserRole;

/**
 * Identity
 *
 * @todo This is a temporary implementation of Rbac
 */
class Identity implements IdentityInterface
{
    /**
     * @var User
     */
    protected $user;

    protected $roles;

    /**
     * @var array
     */
    protected $usersRoles = [
        1 => ['internal-limited-read-only'],
        2 => ['internal-read-only'],
        3 => ['internal-case-worker'],
        4 => ['internal-admin'],
        5 => ['operator-admin'],
        6 => ['operator-user'],
        7 => ['operator-tm'],
        8 => ['operator-ebsr'],
        9 => ['partner-admin'],
        10 => ['partner-user'],
        11 => ['local-authority-admin'],
        12 => ['local-authority-user'],
    ];

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the list of roles of this identity
     *
     * @return string[]|\Rbac\Role\RoleInterface[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            /**
            $this->roles = [];
            $userRoles = $this->user->getUserRoles();

            foreach ($userRoles as $userRole) {
                $this->roles[] = $userRole->getRole();
            }*/

            $this->roles = $this->usersRoles[$this->user->getId()];
        }

        return $this->roles;
    }
}
