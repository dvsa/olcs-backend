<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
 *        @ORM\Index(name="ix_companies_house_company_company_status", columns={"company_status"}),
 *        @ORM\Index(name="ix_companies_house_company_country", columns={"country"})
 *    }
 * )
 */
class CompaniesHouseCompany implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
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
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="company_status", referencedColumnName="id", nullable=true)
     */
    protected $companyStatus;

    /**
     * Country
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", nullable=true)
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
     * @param \Olcs\Db\Entity\RefData $companyStatus
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getCompanyStatus()
    {
        return $this->companyStatus;
    }

    /**
     * Set the country
     *
     * @param \Olcs\Db\Entity\RefData $country
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
     * @return \Olcs\Db\Entity\RefData
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
}
