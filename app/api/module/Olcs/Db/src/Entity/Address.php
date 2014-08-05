<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Address Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="address",
 *    indexes={
 *        @ORM\Index(name="fk_address_country1_idx", columns={"country_code"}),
 *        @ORM\Index(name="fk_address_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_address_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_address_admin_area_traffic_area1_idx", columns={"admin_area"})
 *    }
 * )
 */
class Address implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CountryCodeManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Admin area
     *
     * @var \Olcs\Db\Entity\AdminAreaTrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\AdminAreaTrafficArea")
     * @ORM\JoinColumn(name="admin_area", referencedColumnName="admin_area")
     */
    protected $adminArea;

    /**
     * Uprn
     *
     * @var int
     *
     * @ORM\Column(type="bigint", name="uprn", nullable=true)
     */
    protected $uprn;

    /**
     * Paon start
     *
     * @var string
     *
     * @ORM\Column(type="string", name="paon_start", length=5, nullable=true)
     */
    protected $paonStart;

    /**
     * Paon end
     *
     * @var string
     *
     * @ORM\Column(type="string", name="paon_end", length=5, nullable=true)
     */
    protected $paonEnd;

    /**
     * Address line2
     *
     * @var string
     *
     * @ORM\Column(type="string", name="paon_desc", length=90, nullable=true)
     */
    protected $addressLine2;

    /**
     * Saon start
     *
     * @var string
     *
     * @ORM\Column(type="string", name="saon_start", length=5, nullable=true)
     */
    protected $saonStart;

    /**
     * Saon end
     *
     * @var string
     *
     * @ORM\Column(type="string", name="saon_end", length=5, nullable=true)
     */
    protected $saonEnd;

    /**
     * Address line1
     *
     * @var string
     *
     * @ORM\Column(type="string", name="saon_desc", length=90, nullable=true)
     */
    protected $addressLine1;

    /**
     * Address line3
     *
     * @var string
     *
     * @ORM\Column(type="string", name="street", length=100, nullable=true)
     */
    protected $addressLine3;

    /**
     * Address line4
     *
     * @var string
     *
     * @ORM\Column(type="string", name="locality", length=35, nullable=true)
     */
    protected $addressLine4;

    /**
     * Town
     *
     * @var string
     *
     * @ORM\Column(type="string", name="town", length=30, nullable=true)
     */
    protected $town;

    /**
     * Postcode
     *
     * @var string
     *
     * @ORM\Column(type="string", name="postcode", length=8, nullable=true)
     */
    protected $postcode;

    /**
     * Set the admin area
     *
     * @param \Olcs\Db\Entity\AdminAreaTrafficArea $adminArea
     * @return \Olcs\Db\Entity\Address
     */
    public function setAdminArea($adminArea)
    {
        $this->adminArea = $adminArea;

        return $this;
    }

    /**
     * Get the admin area
     *
     * @return \Olcs\Db\Entity\AdminAreaTrafficArea
     */
    public function getAdminArea()
    {
        return $this->adminArea;
    }

    /**
     * Set the uprn
     *
     * @param int $uprn
     * @return \Olcs\Db\Entity\Address
     */
    public function setUprn($uprn)
    {
        $this->uprn = $uprn;

        return $this;
    }

    /**
     * Get the uprn
     *
     * @return int
     */
    public function getUprn()
    {
        return $this->uprn;
    }

    /**
     * Set the paon start
     *
     * @param string $paonStart
     * @return \Olcs\Db\Entity\Address
     */
    public function setPaonStart($paonStart)
    {
        $this->paonStart = $paonStart;

        return $this;
    }

    /**
     * Get the paon start
     *
     * @return string
     */
    public function getPaonStart()
    {
        return $this->paonStart;
    }

    /**
     * Set the paon end
     *
     * @param string $paonEnd
     * @return \Olcs\Db\Entity\Address
     */
    public function setPaonEnd($paonEnd)
    {
        $this->paonEnd = $paonEnd;

        return $this;
    }

    /**
     * Get the paon end
     *
     * @return string
     */
    public function getPaonEnd()
    {
        return $this->paonEnd;
    }

    /**
     * Set the address line2
     *
     * @param string $addressLine2
     * @return \Olcs\Db\Entity\Address
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    /**
     * Get the address line2
     *
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * Set the saon start
     *
     * @param string $saonStart
     * @return \Olcs\Db\Entity\Address
     */
    public function setSaonStart($saonStart)
    {
        $this->saonStart = $saonStart;

        return $this;
    }

    /**
     * Get the saon start
     *
     * @return string
     */
    public function getSaonStart()
    {
        return $this->saonStart;
    }

    /**
     * Set the saon end
     *
     * @param string $saonEnd
     * @return \Olcs\Db\Entity\Address
     */
    public function setSaonEnd($saonEnd)
    {
        $this->saonEnd = $saonEnd;

        return $this;
    }

    /**
     * Get the saon end
     *
     * @return string
     */
    public function getSaonEnd()
    {
        return $this->saonEnd;
    }

    /**
     * Set the address line1
     *
     * @param string $addressLine1
     * @return \Olcs\Db\Entity\Address
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    /**
     * Get the address line1
     *
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set the address line3
     *
     * @param string $addressLine3
     * @return \Olcs\Db\Entity\Address
     */
    public function setAddressLine3($addressLine3)
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    /**
     * Get the address line3
     *
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->addressLine3;
    }

    /**
     * Set the address line4
     *
     * @param string $addressLine4
     * @return \Olcs\Db\Entity\Address
     */
    public function setAddressLine4($addressLine4)
    {
        $this->addressLine4 = $addressLine4;

        return $this;
    }

    /**
     * Get the address line4
     *
     * @return string
     */
    public function getAddressLine4()
    {
        return $this->addressLine4;
    }

    /**
     * Set the town
     *
     * @param string $town
     * @return \Olcs\Db\Entity\Address
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get the town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set the postcode
     *
     * @param string $postcode
     * @return \Olcs\Db\Entity\Address
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get the postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }
}
