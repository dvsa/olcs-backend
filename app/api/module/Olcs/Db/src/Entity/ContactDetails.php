<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ContactDetails Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="contact_details",
 *    indexes={
 *        @ORM\Index(name="fk_contact_details_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_contact_details_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_contact_details_person1_idx", 
 *            columns={"person_id"}),
 *        @ORM\Index(name="fk_contact_details_address1_idx", 
 *            columns={"address_id"}),
 *        @ORM\Index(name="fk_contact_details_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_contact_details_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_contact_details_ref_data1_idx", 
 *            columns={"contact_type"})
 *    }
 * )
 */
class ContactDetails implements Interfaces\EntityInterface
{

    /**
     * Contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="contact_type", referencedColumnName="id", nullable=false)
     */
    protected $contactType;

    /**
     * Person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person", fetch="LAZY", cascade={"persist"}, inversedBy="contactDetails")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    protected $person;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="contactDetails")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY", inversedBy="contactDetails")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true)
     */
    protected $organisation;

    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", fetch="LAZY", inversedBy="contactDetails")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * Fao
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fao", length=90, nullable=true)
     */
    protected $fao;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=40, nullable=true)
     */
    protected $forename;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=40, nullable=true)
     */
    protected $familyName;

    /**
     * Written permission to engage
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="written_permission_to_engage", nullable=false)
     */
    protected $writtenPermissionToEngage = 0;

    /**
     * Phone contact
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PhoneContact", mappedBy="contactDetails")
     */
    protected $phoneContacts;

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
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=60, nullable=true)
     */
    protected $emailAddress;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->phoneContacts = new ArrayCollection();
    }

    /**
     * Set the contact type
     *
     * @param \Olcs\Db\Entity\RefData $contactType
     * @return ContactDetails
     */
    public function setContactType($contactType)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get the contact type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * Set the person
     *
     * @param \Olcs\Db\Entity\Person $person
     * @return ContactDetails
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get the person
     *
     * @return \Olcs\Db\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return ContactDetails
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return ContactDetails
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
     * Set the address
     *
     * @param \Olcs\Db\Entity\Address $address
     * @return ContactDetails
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the address
     *
     * @return \Olcs\Db\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the fao
     *
     * @param string $fao
     * @return ContactDetails
     */
    public function setFao($fao)
    {
        $this->fao = $fao;

        return $this;
    }

    /**
     * Get the fao
     *
     * @return string
     */
    public function getFao()
    {
        return $this->fao;
    }

    /**
     * Set the forename
     *
     * @param string $forename
     * @return ContactDetails
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get the forename
     *
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set the family name
     *
     * @param string $familyName
     * @return ContactDetails
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set the written permission to engage
     *
     * @param string $writtenPermissionToEngage
     * @return ContactDetails
     */
    public function setWrittenPermissionToEngage($writtenPermissionToEngage)
    {
        $this->writtenPermissionToEngage = $writtenPermissionToEngage;

        return $this;
    }

    /**
     * Get the written permission to engage
     *
     * @return string
     */
    public function getWrittenPermissionToEngage()
    {
        return $this->writtenPermissionToEngage;
    }

    /**
     * Set the phone contact
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $phoneContacts
     * @return ContactDetails
     */
    public function setPhoneContacts($phoneContacts)
    {
        $this->phoneContacts = $phoneContacts;

        return $this;
    }

    /**
     * Get the phone contacts
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPhoneContacts()
    {
        return $this->phoneContacts;
    }

    /**
     * Add a phone contacts
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $phoneContacts
     * @return ContactDetails
     */
    public function addPhoneContacts($phoneContacts)
    {
        if ($phoneContacts instanceof ArrayCollection) {
            $this->phoneContacts = new ArrayCollection(
                array_merge(
                    $this->phoneContacts->toArray(),
                    $phoneContacts->toArray()
                )
            );
        } elseif (!$this->phoneContacts->contains($phoneContacts)) {
            $this->phoneContacts->add($phoneContacts);
        }

        return $this;
    }

    /**
     * Remove a phone contacts
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $phoneContacts
     * @return ContactDetails
     */
    public function removePhoneContacts($phoneContacts)
    {
        if ($this->phoneContacts->contains($phoneContacts)) {
            $this->phoneContacts->removeElement($phoneContacts);
        }

        return $this;
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the email address
     *
     * @param string $emailAddress
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->deletedDate);
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
