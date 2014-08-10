<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait AddressManyToOne
{
    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * Set the address
     *
     * @param \Olcs\Db\Entity\Address $address
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
