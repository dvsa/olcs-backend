<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\SoftDeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="user",
 *    indexes={
 *        @ORM\Index(name="ix_user_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_user_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_user_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_user_local_authority_id", columns={"local_authority_id"}),
 *        @ORM\Index(name="ix_user_os_type", columns={"os_type"}),
 *        @ORM\Index(name="ix_user_partner_contact_details_id",
     *     columns={"partner_contact_details_id"}),
 *        @ORM\Index(name="ix_user_team_id", columns={"team_id"}),
 *        @ORM\Index(name="ix_user_transport_manager_id", columns={"transport_manager_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_user_login_id", columns={"login_id"}),
 *        @ORM\UniqueConstraint(name="uk_user_pid", columns={"pid"})
 *    }
 * )
 */
abstract class AbstractUser implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;
    use SoftDeletableTrait;

    /**
     * Account disabled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="account_disabled", nullable=false, options={"default": 0})
     */
    protected $accountDisabled = 0;

    /**
     * Contact details
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails",
     *     fetch="LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $contactDetails;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
     */
    protected $createdBy;

    /**
     * Disabled date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="disabled_date", nullable=true)
     */
    protected $disabledDate;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last login at
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_login_at", nullable=true)
     */
    protected $lastLoginAt;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Local authority
     *
     * @var \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Bus\LocalAuthority",
     *     fetch="LAZY",
     *     inversedBy="users"
     * )
     * @ORM\JoinColumn(name="local_authority_id", referencedColumnName="id", nullable=true)
     */
    protected $localAuthority;

    /**
     * Login id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="login_id", length=40, nullable=true)
     */
    protected $loginId;

    /**
     * Os type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="os_type", referencedColumnName="id", nullable=true)
     */
    protected $osType;

    /**
     * Partner contact details
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails",
     *     fetch="LAZY",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="partner_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $partnerContactDetails;

    /**
     * Pid
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pid", length=255, nullable=true)
     */
    protected $pid;

    /**
     * Role
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\User\Role", inversedBy="users", fetch="LAZY")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={
     *         @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $roles;

    /**
     * Team
     *
     * @var \Dvsa\Olcs\Api\Entity\User\Team
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     */
    protected $team;

    /**
     * Translate to welsh
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="translate_to_welsh", nullable=false, options={"default": 0})
     */
    protected $translateToWelsh = 0;

    /**
     * Transport manager
     *
     * @var \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManager",
     *     fetch="LAZY",
     *     inversedBy="users"
     * )
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManager;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Organisation user
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser",
     *     mappedBy="user",
     *     cascade={"persist"},
     *     indexBy="organisation_id",
     *     orphanRemoval=true
     * )
     */
    protected $organisationUsers;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->roles = new ArrayCollection();
        $this->organisationUsers = new ArrayCollection();
    }

    /**
     * Set the account disabled
     *
     * @param string $accountDisabled new value being set
     *
     * @return User
     */
    public function setAccountDisabled($accountDisabled)
    {
        $this->accountDisabled = $accountDisabled;

        return $this;
    }

    /**
     * Get the account disabled
     *
     * @return string
     */
    public function getAccountDisabled()
    {
        return $this->accountDisabled;
    }

    /**
     * Set the contact details
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $contactDetails entity being set as the value
     *
     * @return User
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return User
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the disabled date
     *
     * @param \DateTime $disabledDate new value being set
     *
     * @return User
     */
    public function setDisabledDate($disabledDate)
    {
        $this->disabledDate = $disabledDate;

        return $this;
    }

    /**
     * Get the disabled date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDisabledDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->disabledDate);
        }

        return $this->disabledDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last login at
     *
     * @param \DateTime $lastLoginAt new value being set
     *
     * @return User
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * Get the last login at
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastLoginAt($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastLoginAt);
        }

        return $this->lastLoginAt;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return User
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the local authority
     *
     * @param \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority $localAuthority entity being set as the value
     *
     * @return User
     */
    public function setLocalAuthority($localAuthority)
    {
        $this->localAuthority = $localAuthority;

        return $this;
    }

    /**
     * Get the local authority
     *
     * @return \Dvsa\Olcs\Api\Entity\Bus\LocalAuthority
     */
    public function getLocalAuthority()
    {
        return $this->localAuthority;
    }

    /**
     * Set the login id
     *
     * @param string $loginId new value being set
     *
     * @return User
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;

        return $this;
    }

    /**
     * Get the login id
     *
     * @return string
     */
    public function getLoginId()
    {
        return $this->loginId;
    }

    /**
     * Set the os type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $osType entity being set as the value
     *
     * @return User
     */
    public function setOsType($osType)
    {
        $this->osType = $osType;

        return $this;
    }

    /**
     * Get the os type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getOsType()
    {
        return $this->osType;
    }

    /**
     * Set the partner contact details
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $partnerContactDetails entity being set as the value
     *
     * @return User
     */
    public function setPartnerContactDetails($partnerContactDetails)
    {
        $this->partnerContactDetails = $partnerContactDetails;

        return $this;
    }

    /**
     * Get the partner contact details
     *
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails
     */
    public function getPartnerContactDetails()
    {
        return $this->partnerContactDetails;
    }

    /**
     * Set the pid
     *
     * @param string $pid new value being set
     *
     * @return User
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get the pid
     *
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the role
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles collection being set as the value
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the roles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles collection being added
     *
     * @return User
     */
    public function addRoles($roles)
    {
        if ($roles instanceof ArrayCollection) {
            $this->roles = new ArrayCollection(
                array_merge(
                    $this->roles->toArray(),
                    $roles->toArray()
                )
            );
        } elseif (!$this->roles->contains($roles)) {
            $this->roles->add($roles);
        }

        return $this;
    }

    /**
     * Remove a roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles collection being removed
     *
     * @return User
     */
    public function removeRoles($roles)
    {
        if ($this->roles->contains($roles)) {
            $this->roles->removeElement($roles);
        }

        return $this;
    }

    /**
     * Set the team
     *
     * @param \Dvsa\Olcs\Api\Entity\User\Team $team entity being set as the value
     *
     * @return User
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Dvsa\Olcs\Api\Entity\User\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the translate to welsh
     *
     * @param string $translateToWelsh new value being set
     *
     * @return User
     */
    public function setTranslateToWelsh($translateToWelsh)
    {
        $this->translateToWelsh = $translateToWelsh;

        return $this;
    }

    /**
     * Get the translate to welsh
     *
     * @return string
     */
    public function getTranslateToWelsh()
    {
        return $this->translateToWelsh;
    }

    /**
     * Set the transport manager
     *
     * @param \Dvsa\Olcs\Api\Entity\Tm\TransportManager $transportManager entity being set as the value
     *
     * @return User
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return User
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the organisation user
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers collection being set as the value
     *
     * @return User
     */
    public function setOrganisationUsers($organisationUsers)
    {
        $this->organisationUsers = $organisationUsers;

        return $this;
    }

    /**
     * Get the organisation users
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrganisationUsers()
    {
        return $this->organisationUsers;
    }

    /**
     * Add a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers collection being added
     *
     * @return User
     */
    public function addOrganisationUsers($organisationUsers)
    {
        if ($organisationUsers instanceof ArrayCollection) {
            $this->organisationUsers = new ArrayCollection(
                array_merge(
                    $this->organisationUsers->toArray(),
                    $organisationUsers->toArray()
                )
            );
        } elseif (!$this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->add($organisationUsers);
        }

        return $this;
    }

    /**
     * Remove a organisation users
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers collection being removed
     *
     * @return User
     */
    public function removeOrganisationUsers($organisationUsers)
    {
        if ($this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->removeElement($organisationUsers);
        }

        return $this;
    }
}
