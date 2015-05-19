<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseCompany Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_company",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_company_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_companies_house_company_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class CompaniesHouseCompany implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Address line1
     *
     * @var string
     *
     * @ORM\Column(type="string", name="address_line_1", length=100, nullable=true)
     */
    protected $addressLine1;

    /**
     * Address line2
     *
     * @var string
     *
     * @ORM\Column(type="string", name="address_line_2", length=100, nullable=true)
     */
    protected $addressLine2;

    /**
     * Company name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_name", length=255, nullable=true)
     */
    protected $companyName;

    /**
     * Company number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_number", length=8, nullable=false)
     */
    protected $companyNumber;

    /**
     * Company status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_status", length=32, nullable=true)
     */
    protected $companyStatus;

    /**
     * Country
     *
     * @var string
     *
     * @ORM\Column(type="string", name="country", length=32, nullable=true)
     */
    protected $country;

    /**
     * Locality
     *
     * @var string
     *
     * @ORM\Column(type="string", name="locality", length=100, nullable=true)
     */
    protected $locality;

    /**
     * Po box
     *
     * @var string
     *
     * @ORM\Column(type="string", name="po_box", length=100, nullable=true)
     */
    protected $poBox;

    /**
     * Postal code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="postal_code", length=10, nullable=true)
     */
    protected $postalCode;

    /**
     * Premises
     *
     * @var string
     *
     * @ORM\Column(type="string", name="premises", length=100, nullable=true)
     */
    protected $premises;

    /**
     * Region
     *
     * @var string
     *
     * @ORM\Column(type="string", name="region", length=100, nullable=true)
     */
    protected $region;

    /**
     * Officer
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\CompaniesHouseOfficer", mappedBy="company", cascade={"persist"})
     */
    protected $officers;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->officers = new ArrayCollection();
    }

    /**
     * Set the address line1
     *
     * @param string $addressLine1
     * @return CompaniesHouseCompany
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
     * Set the address line2
     *
     * @param string $addressLine2
     * @return CompaniesHouseCompany
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
     * Set the company name
     *
     * @param string $companyName
     * @return CompaniesHouseCompany
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the company name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set the company number
     *
     * @param string $companyNumber
     * @return CompaniesHouseCompany
     */
    public function setCompanyNumber($companyNumber)
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    /**
     * Get the company number
     *
     * @return string
     */
    public function getCompanyNumber()
    {
        return $this->companyNumber;
    }

    /**
     * Set the company status
     *
     * @param string $companyStatus
     * @return CompaniesHouseCompany
     */
    public function setCompanyStatus($companyStatus)
    {
        $this->companyStatus = $companyStatus;

        return $this;
    }

    /**
     * Get the company status
     *
     * @return string
     */
    public function getCompanyStatus()
    {
        return $this->companyStatus;
    }

    /**
     * Set the country
     *
     * @param string $country
     * @return CompaniesHouseCompany
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the locality
     *
     * @param string $locality
     * @return CompaniesHouseCompany
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
     * Set the po box
     *
     * @param string $poBox
     * @return CompaniesHouseCompany
     */
    public function setPoBox($poBox)
    {
        $this->poBox = $poBox;

        return $this;
    }

    /**
     * Get the po box
     *
     * @return string
     */
    public function getPoBox()
    {
        return $this->poBox;
    }

    /**
     * Set the postal code
     *
     * @param string $postalCode
     * @return CompaniesHouseCompany
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get the postal code
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the premises
     *
     * @param string $premises
     * @return CompaniesHouseCompany
     */
    public function setPremises($premises)
    {
        $this->premises = $premises;

        return $this;
    }

    /**
     * Get the premises
     *
     * @return string
     */
    public function getPremises()
    {
        return $this->premises;
    }

    /**
     * Set the region
     *
     * @param string $region
     * @return CompaniesHouseCompany
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get the region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the officer
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $officers
     * @return CompaniesHouseCompany
     */
    public function setOfficers($officers)
    {
        $this->officers = $officers;

        return $this;
    }

    /**
     * Get the officers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOfficers()
    {
        return $this->officers;
    }

    /**
     * Add a officers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $officers
     * @return CompaniesHouseCompany
     */
    public function addOfficers($officers)
    {
        if ($officers instanceof ArrayCollection) {
            $this->officers = new ArrayCollection(
                array_merge(
                    $this->officers->toArray(),
                    $officers->toArray()
                )
            );
        } elseif (!$this->officers->contains($officers)) {
            $this->officers->add($officers);
        }

        return $this;
    }

    /**
     * Remove a officers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $officers
     * @return CompaniesHouseCompany
     */
    public function removeOfficers($officers)
    {
        if ($this->officers->contains($officers)) {
            $this->officers->removeElement($officers);
        }

        return $this;
    }
}
