<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\User\Team;

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
        if (isset($data['userType']) && ($this->getUserType() !== $data['userType'])) {
            // update user type
            $this->userType = $data['userType'];

            // clear all user type specific fields
            $this->team = null;
            $this->transportManager = null;
            $this->partnerContactDetails = null;
            $this->localAuthority = null;
            // TODO - remove link to organisationUser
        }

        $this->loginId = $data['loginId'];

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
            $this->accountDisabled = $data['accountDisabled'];

            if ($this->accountDisabled === 'Y') {
                // set locked date to now
                $this->lockedDate = new \DateTime();
            }
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
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     */
    private function updateInternal(array $data)
    {
        if (isset($data['team'])) {
            $this->team = $data['team'];
        }
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     */
    private function updateTransportManager(array $data)
    {
        if (isset($data['transportManager'])) {
            $this->transportManager = $data['transportManager'];
        }
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     */
    private function updatePartner(array $data)
    {
        if (isset($data['partnerContactDetails'])) {
            $this->partnerContactDetails = $data['partnerContactDetails'];
        }
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     */
    private function updateLocalAuthority(array $data)
    {
        if (isset($data['localAuthority'])) {
            $this->localAuthority = $data['localAuthority'];
        }
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     */
    private function updateSelfService(array $data)
    {
        // TODO - link to organisationUser using $licenceNumber
    }

    /**
     * Get the user type
     *
     * @return string
     */
    public function getUserType()
    {
        if ($this->userType === null) {
            $this->populateUserType();
        }
        return $this->userType;
    }

    /**
     * Uses existing data to populate the user type
     *
     * @return User
     */
    private function populateUserType()
    {
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

        return $this;
    }
}
