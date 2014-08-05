<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OrganisationPerson Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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
        Traits\OrganisationManyToOne,
        Traits\PersonManyToOne,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Set the added date
     *
     * @param string $addedDate
     * @return \Olcs\Db\Entity\OrganisationPerson
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
     * @return \Olcs\Db\Entity\OrganisationPerson
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
