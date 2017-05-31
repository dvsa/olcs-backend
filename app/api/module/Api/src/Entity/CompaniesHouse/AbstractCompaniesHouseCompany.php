<?php

namespace Dvsa\Olcs\Api\Entity\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CompaniesHouseCompany Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_company",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_company_company_number", columns={"company_number"})
 *    }
 * )
 */
abstract class AbstractCompaniesHouseCompany implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Officer
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseOfficer",
     *     mappedBy="companiesHouseCompany",
     *     cascade={"persist"}
     * )
     */
    protected $officers;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->officers = new ArrayCollection();
    }

    /**
     * Set the address line1
     *
     * @param string $addressLine1 new value being set
     *
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
     * @param string $addressLine2 new value being set
     *
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
     * @param string $companyName new value being set
     *
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
     * @param string $companyNumber new value being set
     *
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
     * @param string $companyStatus new value being set
     *
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
     * @param string $country new value being set
     *
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
     * Set the created on
     *
     * @param \DateTime $createdOn new value being set
     *
     * @return CompaniesHouseCompany
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return CompaniesHouseCompany
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return CompaniesHouseCompany
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModifiedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModifiedOn);
        }

        return $this->lastModifiedOn;
    }

    /**
     * Set the locality
     *
     * @param string $locality new value being set
     *
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
     * @param string $poBox new value being set
     *
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
     * @param string $postalCode new value being set
     *
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
     * @param string $premises new value being set
     *
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
     * @param string $region new value being set
     *
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
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return CompaniesHouseCompany
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the officer
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $officers collection being set as the value
     *
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
     * @param \Doctrine\Common\Collections\ArrayCollection $officers collection being added
     *
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
     * @param \Doctrine\Common\Collections\ArrayCollection $officers collection being removed
     *
     * @return CompaniesHouseCompany
     */
    public function removeOfficers($officers)
    {
        if ($this->officers->contains($officers)) {
            $this->officers->removeElement($officers);
        }

        return $this;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {
                    $this->$property = new ArrayCollection(array());
                } else {
                    $this->$property = null;
                }
            }
        }
    }
}
