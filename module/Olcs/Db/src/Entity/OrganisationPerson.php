<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrganisationPerson Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="organisation_person",
 *    indexes={
 *        @ORM\Index(name="fk_owner_person1_idx", columns={"person_id"}),
 *        @ORM\Index(name="fk_owner_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_owner_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_owner_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class OrganisationPerson implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person", fetch="LAZY")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     */
    protected $person;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY", inversedBy="organisationPersons")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Added date
     *
     * @var string
     *
     * @ORM\Column(type="string", name="added_date", length=45, nullable=true)
     */
    protected $addedDate;

    /**
     * Position
     *
     * @var string
     *
     * @ORM\Column(type="string", name="position", length=45, nullable=true)
     */
    protected $position;

    /**
     * Set the person
     *
     * @param \Olcs\Db\Entity\Person $person
     * @return OrganisationPerson
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
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return OrganisationPerson
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
     * Set the added date
     *
     * @param string $addedDate
     * @return OrganisationPerson
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return string
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }


    /**
     * Set the position
     *
     * @param string $position
     * @return OrganisationPerson
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

}
