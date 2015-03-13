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
 *        @ORM\Index(name="ix_organisation_user_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_organisation_user_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_organisation_user_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_CFD7D6519E6B1585", columns={"organisation_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_organisation_user_organisation_id_user_id", columns={"organisation_id","user_id"})
 *    }
 * )
 */
class OrganisationUser implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OrganisationManyToOne,
        Traits\RemovedDateField,
        Traits\CustomVersionField;

    /**
     * Added date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="added_date", nullable=true)
     */
    protected $addedDate;

    /**
     * Is administrator
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_administrator", nullable=false, options={"default": 0})
     */
    protected $isAdministrator = 0;

    /**
     * Sftp access
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sftp_access", nullable=false, options={"default": 0})
     */
    protected $sftpAccess = 0;

    /**
     * User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", inversedBy="organisationUsers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return OrganisationUser
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
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
}
