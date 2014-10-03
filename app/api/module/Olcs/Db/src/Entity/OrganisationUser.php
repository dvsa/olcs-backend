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
 *        @ORM\Index(name="IDX_CFD7D651A76ED395", columns={"user_id"}),
 *        @ORM\Index(name="IDX_CFD7D651DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_CFD7D6519E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_CFD7D65165CF370E", columns={"last_modified_by"})
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
        Traits\CreatedByManyToOne,
        Traits\OrganisationManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
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
    protected $isAdministrator;

    /**
     * Sftp access
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sftp_access", nullable=false)
     */
    protected $sftpAccess;

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
