<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
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
 *        @ORM\Index(name="fk_contact_details_person1_idx", columns={"person_id"}),
 *        @ORM\Index(name="fk_contact_details_address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_contact_details_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_contact_details_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_contact_details_ref_data1_idx", columns={"contact_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_olbs_key_etl", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class ContactDetails implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\Description255FieldAlt1,
        Traits\EmailAddress60Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", cascade={"persist"}, inversedBy="contactDetails")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * Contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="contact_type", referencedColumnName="id", nullable=false)
     */
    protected $contactType;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=40, nullable=true)
     */
    protected $familyName;

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
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=32, nullable=true)
     */
    protected $olbsType;

    /**
     * Person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person", cascade={"persist"}, inversedBy="contactDetails")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    protected $person;

    /**
     * Written permission to engage
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="written_permission_to_engage", nullable=false, options={"default": 0})
     */
    protected $writtenPermissionToEngage;

    /**
     * Phone contact
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PhoneContact", mappedBy="contactDetails", cascade={"persist"})
     */
    protected $phoneContacts;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->phoneContacts = new ArrayCollection();
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
     * Set the olbs type
     *
     * @param string $olbsType
     * @return ContactDetails
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
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
}
