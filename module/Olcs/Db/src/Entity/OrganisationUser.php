<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OrganisationUser Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="organisation_user",
 *    indexes={
 *        @ORM\Index(name="fk_organisation_has_user_user1_idx", columns={"user_id"}),
 *        @ORM\Index(name="fk_organisation_has_user_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_organisation_user_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_organisation_user_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="organisation_user_unique", columns={"organisation_id","user_id"})
 *    }
 * )
 */
class OrganisationUser implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OrganisationManyToOneAlt1,
        Traits\AddedDateField,
        Traits\RemovedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY", inversedBy="organisationUsers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Is administrator
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_administrator", nullable=false)
     */
    protected $isAdministrator = 0;

    /**
     * Sftp access
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sftp_access", nullable=false)
     */
    protected $sftpAccess = 0;

    /**
     * Set the user
     *
     * @param \Olcs\Db\Entity\User $user
     * @return OrganisationUser
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set the is administrator
     *
     * @param string $isAdministrator
     * @return OrganisationUser
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get the is administrator
     *
     * @return string
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }


    /**
     * Set the sftp access
     *
     * @param string $sftpAccess
     * @return OrganisationUser
     */
    public function setSftpAccess($sftpAccess)
    {
        $this->sftpAccess = $sftpAccess;

        return $this;
    }

    /**
     * Get the sftp access
     *
     * @return string
     */
    public function getSftpAccess()
    {
        return $this->sftpAccess;
    }

}
