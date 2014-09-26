<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Team Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="team",
 *    indexes={
 *        @ORM\Index(name="IDX_C4E0A61FF508DBD2", columns={"override_ta_contact_id"}),
 *        @ORM\Index(name="IDX_C4E0A61FDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C4E0A61F65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_C4E0A61F18E0B1DB", columns={"traffic_area_id"})
 *    }
 * )
 */
class Team implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\Description255FieldAlt1,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Override ta contact
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY")
     * @ORM\JoinColumn(name="override_ta_contact_id", referencedColumnName="id", nullable=true)
     */
    protected $overrideTaContact;

    /**
     * Set the override ta contact
     *
     * @param \Olcs\Db\Entity\ContactDetails $overrideTaContact
     * @return Team
     */
    public function setOverrideTaContact($overrideTaContact)
    {
        $this->overrideTaContact = $overrideTaContact;

        return $this;
    }

    /**
     * Get the override ta contact
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getOverrideTaContact()
    {
        return $this->overrideTaContact;
    }
}
