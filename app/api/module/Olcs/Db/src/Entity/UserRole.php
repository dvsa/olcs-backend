<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * UserRole Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_role",
 *    indexes={
 *        @ORM\Index(name="fk_user_has_role_role1_idx", columns={"role_id"}),
 *        @ORM\Index(name="fk_user_has_role_user1_idx", columns={"user_id"}),
 *        @ORM\Index(name="fk_user_role_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_user_role_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class UserRole implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\RoleManyToOne,
        Traits\UserManyToOne,
        Traits\CustomVersionField;

    /**
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="expiry_date", nullable=true)
     */
    protected $expiryDate;

    /**
     * Valid from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="valid_from", nullable=true)
     */
    protected $validFrom;

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate
     * @return UserRole
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set the valid from
     *
     * @param \DateTime $validFrom
     * @return UserRole
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get the valid from
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }
}
