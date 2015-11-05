<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\User\Role as RoleEntity;

/**
 * User Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user",
 *    indexes={
 *        @ORM\Index(name="ix_user_team_id", columns={"team_id"}),
 *        @ORM\Index(name="ix_user_local_authority_id", columns={"local_authority_id"}),
 *        @ORM\Index(name="ix_user_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_user_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_user_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_user_partner_contact_details_id", columns={"partner_contact_details_id"}),
 *        @ORM\Index(name="ix_user_transport_manager_id", columns={"transport_manager_id"})
 *    }
 * )
 */
class User extends AbstractUser implements OrganisationProviderInterface
{
    const PERMISSION_ADMIN = 'admin';
    const PERMISSION_USER = 'user';
    const PERMISSION_TM = 'tm';

    const USER_TYPE_INTERNAL = 'internal';
    const USER_TYPE_LOCAL_AUTHORITY = 'local-authority';
    const USER_TYPE_OPERATOR = 'operator';
    const USER_TYPE_PARTNER = 'partner';
    const USER_TYPE_TRANSPORT_MANAGER = 'transport-manager';

    const ERROR_ADMIN_USER_ALREADY_EXISTS = 'err_admin_user_already_exists';

    /**
     * List of all roles available by user type
     *
     * @var array
     */
    private static $rolesAvailableByUserType = [
        self::USER_TYPE_LOCAL_AUTHORITY => [
            RoleEntity::ROLE_LOCAL_AUTHORITY_ADMIN,
            RoleEntity::ROLE_LOCAL_AUTHORITY_USER,
        ],
        self::USER_TYPE_OPERATOR => [
            RoleEntity::ROLE_OPERATOR_ADMIN,
            RoleEntity::ROLE_OPERATOR_USER,
            RoleEntity::ROLE_OPERATOR_TM,
        ],
        self::USER_TYPE_TRANSPORT_MANAGER => [
            RoleEntity::ROLE_OPERATOR_ADMIN,
            RoleEntity::ROLE_OPERATOR_USER,
            RoleEntity::ROLE_OPERATOR_TM,
        ],
        self::USER_TYPE_PARTNER => [
            RoleEntity::ROLE_PARTNER_ADMIN,
            RoleEntity::ROLE_PARTNER_USER,
        ],
        self::USER_TYPE_INTERNAL => [
            RoleEntity::ROLE_INTERNAL_ADMIN,
            RoleEntity::ROLE_INTERNAL_CASE_WORKER,
            RoleEntity::ROLE_INTERNAL_READ_ONLY,
            RoleEntity::ROLE_INTERNAL_LIMITED_READ_ONLY,
        ],
    ];

    /**
     * List of roles by user type and permission
     *
     * @var array
     */
    private static $userTypeToRoles = [
        self::USER_TYPE_LOCAL_AUTHORITY => [
            self::PERMISSION_ADMIN => [RoleEntity::ROLE_LOCAL_AUTHORITY_ADMIN],
            self::PERMISSION_USER => [RoleEntity::ROLE_LOCAL_AUTHORITY_USER],
        ],
        self::USER_TYPE_OPERATOR => [
            self::PERMISSION_ADMIN => [RoleEntity::ROLE_OPERATOR_ADMIN],
            self::PERMISSION_USER => [RoleEntity::ROLE_OPERATOR_USER],
            self::PERMISSION_TM => [RoleEntity::ROLE_OPERATOR_TM],
        ],
        self::USER_TYPE_TRANSPORT_MANAGER => [
            self::PERMISSION_ADMIN => [RoleEntity::ROLE_OPERATOR_ADMIN],
            self::PERMISSION_USER => [RoleEntity::ROLE_OPERATOR_USER],
            self::PERMISSION_TM => [RoleEntity::ROLE_OPERATOR_TM],
        ],
        self::USER_TYPE_PARTNER => [
            self::PERMISSION_ADMIN => [RoleEntity::ROLE_PARTNER_ADMIN],
            self::PERMISSION_USER => [RoleEntity::ROLE_PARTNER_USER],
        ],
    ];

    /**
     * User type
     *
     * @var string
     */
    protected $userType = null;

    public function __construct($userType)
    {
        parent::__construct();
        $this->userType = $userType;
    }

    /**
     * @param string $userType
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    public static function create($userType, $data)
    {
        $user = new static($userType);
        $user->update($data);

        return $user;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    public function update(array $data)
    {
        // update common data
        $this->loginId = $data['loginId'];

        if (isset($data['userType'])) {
            $this->updateUserType($data['userType']);
        }

        if (isset($data['roles'])) {
            $this->updateRoles($data['roles']);
        }

        if (isset($data['accountDisabled'])) {
            $this->updateAccountDisabled($data['accountDisabled']);
        }

        // each type may have different update
        switch($this->getUserType()) {
            case self::USER_TYPE_INTERNAL:
                $this->updateInternal($data);
                break;
            case self::USER_TYPE_TRANSPORT_MANAGER:
                $this->updateTransportManager($data);
                break;
            case self::USER_TYPE_PARTNER:
                $this->updatePartner($data);
                break;
            case self::USER_TYPE_LOCAL_AUTHORITY:
                $this->updateLocalAuthority($data);
                break;
            case self::USER_TYPE_OPERATOR:
                $this->updateOperator($data);
                break;
        }

        return $this;
    }

    /**
     * @param string $userType
     * @return User
     */
    private function updateUserType($userType)
    {
        if ($this->getUserType() !== $userType) {
            // update user type
            $this->userType = $userType;

            // reset all user type specific fields
            $this->team = null;
            $this->transportManager = null;
            $this->partnerContactDetails = null;
            $this->localAuthority = null;

            if (!in_array($userType, [self::USER_TYPE_OPERATOR, self::USER_TYPE_TRANSPORT_MANAGER])) {
                // reset org users only if switching to a user type which does not populate the value anyway
                // otherwise Operator->TM or TM->Operator fails as collection is empty
                $this->populateOrganisationUsers();
            }
        }

        return $this;
    }

    /**
     * @param array $roles
     * @return User
     */
    private function updateRoles(array $roles)
    {
        if (!empty($roles)) {
            // check if there are any roles available for the user type
            if (empty(self::$rolesAvailableByUserType[$this->getUserType()])) {
                throw new ValidationException(['The roles selected are not available to this user type']);
            }

            $rolesAvailable = array_intersect(
                // user's roles selected
                array_map(
                    function ($role) {
                        return $role->getRole();
                    },
                    $roles
                ),
                // list of roles available for the user type
                self::$rolesAvailableByUserType[$this->getUserType()]
            );

            // make sure that all selected roles are available to this user type
            if (count($roles) !== count($rolesAvailable)) {
                throw new ValidationException(['The roles selected are not available to this user type']);
            }
        }

        // set the new roles
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    /**
     * @param string $accountDisabled
     * @return User
     */
    private function updateAccountDisabled($accountDisabled)
    {
        $this->accountDisabled = $accountDisabled;

        if ($this->accountDisabled === 'Y') {
            // set locked date to now
            $this->lockedDate = new \DateTime();
        } else {
            $this->lockedDate = null;
        }

        return $this;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updateInternal(array $data)
    {
        if (isset($data['team'])) {
            $this->team = $data['team'];
        }

        return $this;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updateTransportManager(array $data)
    {
        if (isset($data['transportManager'])) {
            $this->transportManager = $data['transportManager'];
        }
        $this->updateOrganisationUsers($data);

        return $this;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updatePartner(array $data)
    {
        if (isset($data['partnerContactDetails'])) {
            $this->partnerContactDetails = $data['partnerContactDetails'];
        }

        return $this;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updateLocalAuthority(array $data)
    {
        if (isset($data['localAuthority'])) {
            $this->localAuthority = $data['localAuthority'];
        }

        return $this;
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updateOperator(array $data)
    {
        $this->updateOrganisationUsers($data);

        return $this;
    }

    /**
     * Get the user type
     *
     * @return string
     */
    public function getUserType()
    {
        if ($this->userType === null) {
            if (isset($this->team)) {
                $this->userType = self::USER_TYPE_INTERNAL;
            } elseif (isset($this->localAuthority)) {
                $this->userType = self::USER_TYPE_LOCAL_AUTHORITY;
            } elseif (isset($this->transportManager)) {
                $this->userType = self::USER_TYPE_TRANSPORT_MANAGER;
            } elseif (isset($this->partnerContactDetails)) {
                $this->userType = self::USER_TYPE_PARTNER;
            } else {
                $this->userType = self::USER_TYPE_OPERATOR;
            }
        }
        return $this->userType;
    }


    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return User
     */
    private function updateOrganisationUsers(array $data)
    {
        if (isset($data['organisations'])) {
            // update list of organisations
            $this->populateOrganisationUsers($data['organisations']);
        } else {
            // update isAdministrator flag only
            $orgs = array_map(
                function ($organisationUser) {
                    return $organisationUser->getOrganisation();
                },
                $this->getOrganisationUsers()->toArray()
            );

            $this->populateOrganisationUsers($orgs);
        }

        return $this;
    }

    /**
     * @param array $orgs List of Dvsa\Olcs\Api\Entity\Organisation\Organisation
     * @return User
     */
    private function populateOrganisationUsers(array $orgs = null)
    {
        $orgs = isset($orgs) ? $orgs : [];
        $seen = [];

        $collection = $this->getOrganisationUsers()->toArray();

        foreach ($orgs as $org) {
            $id = $org->getId();

            if (!empty($collection[$id])) {
                // update
                $collection[$id]->setIsAdministrator($this->isAdministrator() ? 'Y' : 'N');

                // mark as seen
                $seen[$id] = $id;
            } else {
                // create
                $orgUserEntity = new OrganisationUserEntity();
                $orgUserEntity->setUser($this);
                $orgUserEntity->setOrganisation($org);
                $orgUserEntity->setIsAdministrator($this->isAdministrator() ? 'Y' : 'N');

                $this->organisationUsers->add($orgUserEntity);
            }
        }

        // remove the rest
        foreach (array_diff_key($collection, $seen) as $key => $entity) {
            // unlink
            $this->organisationUsers->remove($key);
        }

        return $this;
    }

    /**
     * @return bool
     */
    private function isAdministrator()
    {
        // is admin if has roles for admin permission
        return $this->hasRoles(
            self::getRolesByUserType($this->getUserType(), self::PERMISSION_ADMIN)
        );
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        if (empty(self::$userTypeToRoles[$this->getUserType()])) {
            return null;
        }

        foreach (self::$userTypeToRoles[$this->getUserType()] as $permission => $roles) {
            if ($this->hasRoles($roles)) {
                return $permission;
            }
        }

        return null;
    }

    /**
     * @param array $roles
     * @return bool
     */
    private function hasRoles(array $roles)
    {
        return !$this->roles->isEmpty() && !empty(
            array_intersect(
                // list of roles to check for
                $roles,
                // user's roles
                array_map(
                    function ($role) {
                        return $role->getRole();
                    },
                    $this->roles->toArray()
                )
            )
        );
    }

    /**
     * @param string $userType
     * @param string $permission
     * @return array
     */
    public static function getRolesByUserType($userType, $permission)
    {
        if (!empty(self::$userTypeToRoles[$userType][$permission])) {
            return self::$userTypeToRoles[$userType][$permission];
        }

        return [];
    }

    public function getRelatedOrganisation()
    {
        if ($this->getOrganisationUsers()->isEmpty()) {
            return null;
        }
        return $this->getOrganisationUsers()->current()->getOrganisation();
    }

    /**
     * Returns related Organisation Name based on the user's type
     *
     * @var string
     */
    public function getRelatedOrganisationName()
    {
        $name = '';

        switch($this->getUserType()) {
            case self::USER_TYPE_INTERNAL:
                $name = 'DVSA';
                break;
            case self::USER_TYPE_PARTNER:
                $name = $this->getPartnerContactDetails()->getDescription();
                break;
            case self::USER_TYPE_LOCAL_AUTHORITY:
                $name = $this->getLocalAuthority()->getDescription();
                break;
            default:
                $org = $this->getRelatedOrganisation();

                if ($org !== null) {
                    $name = $org->getName();
                }
                break;
        }

        return $name;
    }
}
