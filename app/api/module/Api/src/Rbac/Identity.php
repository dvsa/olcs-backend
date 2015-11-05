<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Identity\IdentityInterface;

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
        // test (olcs_rollout_data.sql) users
        1 => ['internal-limited-read-only'],
        2 => ['internal-read-only'],
        3 => ['internal-case-worker'],
        4 => ['internal-admin'],
        20 => ['operator-admin'],
        21 => ['operator-user'],
        7 => ['operator-tm'],
        22 => ['partner-admin'],
        23 => ['partner-user'],
        24 => ['local-authority-admin'],
        25 => ['local-authority-user'],
        26 => ['internal-admin'],

        // ETL users
        336 => ['internal-admin'],
        542 => ['operator-admin'],
        42955 => ['operator-admin', 'operator-ebsr'],
    ];

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the list of roles of this identity
     *
     * @return string[]|\Rbac\Role\RoleInterface[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            /*
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
