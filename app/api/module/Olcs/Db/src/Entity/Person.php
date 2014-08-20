<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Person Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="person",
 *    indexes={
 *        @ORM\Index(name="fk_person_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_person_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Person implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\BirthDateField,
        Traits\Title32Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomDeletedDateField,
        Traits\CustomVersionField;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=35, nullable=true)
     */
    protected $forename;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=35, nullable=true)
     */
    protected $familyName;

    /**
     * Birth place
     *
     * @var string
     *
     * @ORM\Column(type="string", name="birth_place", length=35, nullable=true)
     */
    protected $birthPlace;

    /**
     * Other name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_name", length=35, nullable=true)
     */
    protected $otherName;

    /**
     * Title other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="title_other", length=20, nullable=true)
     */
    protected $titleOther;

    /**
     * Contact detail
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ContactDetails", mappedBy="person")
     */
    protected $contactDetails;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->contactDetails = new ArrayCollection();
    }


    /**
     * Set the forename
     *
     * @param string $forename
     * @return Person
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
     * @return Person
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
     * Set the birth place
     *
     * @param string $birthPlace
     * @return Person
     */
    public function setBirthPlace($birthPlace)
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get the birth place
     *
     * @return string
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set the other name
     *
     * @param string $otherName
     * @return Person
     */
    public function setOtherName($otherName)
    {
        $this->otherName = $otherName;

        return $this;
    }

    /**
     * Get the other name
     *
     * @return string
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     * Set the title other
     *
     * @param string $titleOther
     * @return Person
     */
    public function setTitleOther($titleOther)
    {
        $this->titleOther = $titleOther;

        return $this;
    }

    /**
     * Get the title other
     *
     * @return string
     */
    public function getTitleOther()
    {
        return $this->titleOther;
    }

    /**
     * Set the contact detail
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Person
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }

    /**
     * Add a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Person
     */
    public function addContactDetails($contactDetails)
    {
        if ($contactDetails instanceof ArrayCollection) {
            $this->contactDetails = new ArrayCollection(
                array_merge(
                    $this->contactDetails->toArray(),
                    $contactDetails->toArray()
                )
            );
        } elseif (!$this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->add($contactDetails);
        }

        return $this;
    }

    /**
     * Remove a contact details
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $contactDetails
     * @return Person
     */
    public function removeContactDetails($contactDetails)
    {
        if ($this->contactDetails->contains($contactDetails)) {
            $this->contactDetails->remove($contactDetails);
        }

        return $this;
    }
}
