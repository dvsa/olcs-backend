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
     * Paon desc
     *
     * @var string
     *
     * @ORM\Column(type="string", name="paon_desc", length=90, nullable=true)
     */
    protected $paonDesc;

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
     * Saon desc
     *
     * @var string
     *
     * @ORM\Column(type="string", name="saon_desc", length=90, nullable=true)
     */
    protected $saonDesc;

    /**
     * Street
     *
     * @var string
     *
     * @ORM\Column(type="string", name="street", length=100, nullable=true)
     */
    protected $street;

    /**
     * Locality
     *
     * @var string
     *
     * @ORM\Column(type="string", name="locality", length=35, nullable=true)
     */
    protected $locality;

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
     * Set the paon desc
     *
     * @param string $paonDesc
     * @return \Olcs\Db\Entity\Address
     */
    public function setPaonDesc($paonDesc)
    {
        $this->paonDesc = $paonDesc;

        return $this;
    }

    /**
     * Get the paon desc
     *
     * @return string
     */
    public function getPaonDesc()
    {
        return $this->paonDesc;
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
     * Set the saon desc
     *
     * @param string $saonDesc
     * @return \Olcs\Db\Entity\Address
     */
    public function setSaonDesc($saonDesc)
    {
        $this->saonDesc = $saonDesc;

        return $this;
    }

    /**
     * Get the saon desc
     *
     * @return string
     */
    public function getSaonDesc()
    {
        return $this->saonDesc;
    }

    /**
     * Set the street
     *
     * @param string $street
     * @return \Olcs\Db\Entity\Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get the street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the locality
     *
     * @param string $locality
     * @return \Olcs\Db\Entity\Address
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get the locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
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
