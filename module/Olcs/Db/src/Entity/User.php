<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="user",
 *    indexes={
 *        @ORM\Index(name="fk_user_team1_idx", columns={"team_id"}),
 *        @ORM\Index(name="fk_user_local_authority1_idx", columns={"local_authority_id"}),
 *        @ORM\Index(name="fk_user_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_user_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_user_contact_details1_idx", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_user_contact_details2_idx", columns={"partner_contact_details_id"}),
 *        @ORM\Index(name="fk_user_transport_manager1_idx", columns={"transport_manager_id"})
 *    }
 * )
 */
class User implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TransportManagerManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LocalAuthorityManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TeamManyToOne,
        Traits\EmailAddress45Field,
        Traits\Name100Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Partner contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY")
     * @ORM\JoinColumn(name="partner_contact_details_id", referencedColumnName="id")
     */
    protected $partnerContactDetails;

    /**
     * Pid
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="pid", nullable=true)
     */
    protected $pid;

    /**
     * Account disabled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="account_disabled", nullable=false)
     */
    protected $accountDisabled = 0;

    /**
     * Organisation user
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OrganisationUser", mappedBy="user")
     */
    protected $organisationUsers;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->organisationUsers = new ArrayCollection();
    }


    /**
     * Set the partner contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $partnerContactDetails
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
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getPartnerContactDetails()
    {
        return $this->partnerContactDetails;
    }

    /**
     * Set the pid
     *
     * @param int $pid
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
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the account disabled
     *
     * @param string $accountDisabled
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
     * Set the organisation user
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
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
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
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
     * @param \Doctrine\Common\Collections\ArrayCollection $organisationUsers
     * @return User
     */
    public function removeOrganisationUsers($organisationUsers)
    {
        if ($this->organisationUsers->contains($organisationUsers)) {
            $this->organisationUsers->remove($organisationUsers);
        }

        return $this;
    }
}
