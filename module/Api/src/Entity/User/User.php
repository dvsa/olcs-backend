<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
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
 *        @ORM\Index(name="ix_user_hint_question_id1", columns={"hint_question_id1"}),
 *        @ORM\Index(name="ix_user_hint_question_id2", columns={"hint_question_id2"}),
 *        @ORM\Index(name="ix_user_transport_manager_id", columns={"transport_manager_id"})
 *    }
 * )
 */
class User extends AbstractUser
{
    const USER_TYPE_INTERNAL = 'internal';
    const USER_TYPE_LOCAL_AUTHORITY = 'local-authority';
    const USER_TYPE_PARTNER = 'partner';
    const USER_TYPE_SELF_SERVICE = 'self-service';
    const USER_TYPE_TRANSPORT_MANAGER = 'transport-manager';

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
            $this->roles = $data['roles'];
        }

        if (isset($data['memorableWord'])) {
            $this->memorableWord = $data['memorableWord'];
        }

        if (isset($data['mustResetPassword'])) {
            $this->mustResetPassword = $data['mustResetPassword'];
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
            case self::USER_TYPE_SELF_SERVICE:
                $this->updateSelfService($data);
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
            $this->populateOrganisationUsers();
        }

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
    private function updateSelfService(array $data)
    {
        if (isset($data['organisations'])) {
            $this->populateOrganisationUsers($data['organisations']);
        }

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
                $this->userType = self::USER_TYPE_SELF_SERVICE;
            }
        }
        return $this->userType;
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
        // is admin if has "operator-admin" role
        return in_array(
            RoleEntity::ROLE_OPERATOR_ADMIN,
            array_map(
                function ($role) {
                    return $role->getRole();
                },
                $this->roles
            )
        );
    }
}
