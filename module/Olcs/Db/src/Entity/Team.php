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
 *        @ORM\Index(name="fk_team_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_team_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_team_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_team_contact_details1_idx", columns={"override_ta_contact_id"})
 *    }
 * )
 */
class Team implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
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
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="override_ta_contact_id", referencedColumnName="id")
     */
    protected $overrideTaContact;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

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
