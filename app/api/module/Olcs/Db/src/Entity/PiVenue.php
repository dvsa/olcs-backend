<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiVenue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_venue",
 *    indexes={
 *        @ORM\Index(name="fk_pi_venue_address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_pi_venue_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_venue_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_pi_venue_traffic_area1_idx", columns={"traffic_area_id"})
 *    }
 * )
 */
class PiVenue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=false)
     */
    protected $address;

    /**
     * Set the address
     *
     * @param \Olcs\Db\Entity\Address $address
     * @return PiVenue
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
}
