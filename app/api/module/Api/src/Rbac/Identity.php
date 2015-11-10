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

        // more ETL users - OLCS-11144
        ////////////////////////////////////////
        // Scenario    Role    Organisation_id
        ////////////////////////////////////////
        // Goods MLH admin operator-admin  23778
        918 => ['operator-admin'],
        // Goods MLH operator-user operator-user   23778
        919 => ['operator-user'],
        // Goods Partnership   operator-admin  228677
        81799 => ['operator-admin'],
        // Goods Other operator-admin  104731
        35150 => ['operator-admin'], // there were no operator-admins, this user only has operator-user in the db
        // PSV EBSR    operator-ebsr   99832
        43066 => ['operator-ebsr'], // has both operator-ebsr and operator-user in the db
        // PSV EBSR    operator-ebsr   4
        83639 => ['operator-ebsr'], // has both operator-ebsr and operator-user in the db
        // SPR user    operator-admin  240579
        65125 => ['operator-admin'],
        ////////////////////////////////////////
        // selfserve users with other roles
        508 => ['partner-admin'],
        1686 => ['partner-user'],
        17197 => ['local-authority-admin'],
        51578 => ['local-authority-user'],
        ////////////////////////////////////////
        // internal users with other roles
        55 => ['internal-case-worker'],
        57 => ['internal-case-worker'],
        273 => ['internal-admin'],
        291 => ['internal-admin'],
        ////////////////////////////////////////
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
