<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * Title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="title", length=32, nullable=true)
     */
    protected $title;

    /**
     * Title other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="title_other", length=20, nullable=true)
     */
    protected $titleOther;

    /**
     * Set the forename
     *
     * @param string $forename
     * @return \Olcs\Db\Entity\Person
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
     * @return \Olcs\Db\Entity\Person
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
     * @return \Olcs\Db\Entity\Person
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
     * @return \Olcs\Db\Entity\Person
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
     * Set the title
     *
     * @param string $title
     * @return \Olcs\Db\Entity\Person
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title other
     *
     * @param string $titleOther
     * @return \Olcs\Db\Entity\Person
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
}
