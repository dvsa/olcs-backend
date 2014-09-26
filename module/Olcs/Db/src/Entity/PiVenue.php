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
 *        @ORM\Index(name="IDX_7D3711FDF5B7AF75", columns={"address_id"}),
 *        @ORM\Index(name="IDX_7D3711FDDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_7D3711FD65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_7D3711FD18E0B1DB", columns={"traffic_area_id"})
 *    }
 * )
 */
class PiVenue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
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
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", fetch="LAZY")
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
