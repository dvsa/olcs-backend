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
 *    }
 * )
 */
class OrganisationUser implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\AddedDateField,
        Traits\RemovedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Organisation")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     */
    protected $organisation;

    /**
     * Identifier - User
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Is administrator
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_administrator", nullable=false)
     */
    protected $isAdministrator = 0;

    /**
     * Sftp access
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="sftp_access", nullable=false)
     */
    protected $sftpAccess = 0;

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return \Olcs\Db\Entity\OrganisationUser
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the user
     *
     * @param \Olcs\Db\Entity\User $user
     * @return \Olcs\Db\Entity\OrganisationUser
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
     * @param boolean $isAdministrator
     * @return \Olcs\Db\Entity\OrganisationUser
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get the is administrator
     *
     * @return boolean
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Set the sftp access
     *
     * @param boolean $sftpAccess
     * @return \Olcs\Db\Entity\OrganisationUser
     */
    public function setSftpAccess($sftpAccess)
    {
        $this->sftpAccess = $sftpAccess;

        return $this;
    }

    /**
     * Get the sftp access
     *
     * @return boolean
     */
    public function getSftpAccess()
    {
        return $this->sftpAccess;
    }
}
