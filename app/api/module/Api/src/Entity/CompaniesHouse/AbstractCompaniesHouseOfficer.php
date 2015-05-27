<?php

namespace Dvsa\Olcs\Api\Entity\CompaniesHouse;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CompaniesHouseOfficer Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_officer",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_officer_companies_house_company_id",
     *     columns={"company_id"})
 *    }
 * )
 */
abstract class AbstractCompaniesHouseOfficer implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Company
     *
     * @var \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany",
     *     fetch="LAZY",
     *     inversedBy="officers"
     * )
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Date of birth
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_of_birth", nullable=true)
     */
    protected $dateOfBirth;

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
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=100, nullable=true)
     */
    protected $name;

    /**
     * Role
     *
     * @var string
     *
     * @ORM\Column(type="string", name="role", length=32, nullable=true)
     */
    protected $role;

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
     * Set the company
     *
     * @param \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany $company
     * @return CompaniesHouseOfficer
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get the company
     *
     * @return \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return CompaniesHouseOfficer
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the date of birth
     *
     * @param \DateTime $dateOfBirth
     * @return CompaniesHouseOfficer
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get the date of birth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return CompaniesHouseOfficer
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
     * @param \DateTime $lastModifiedOn
     * @return CompaniesHouseOfficer
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return CompaniesHouseOfficer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the role
     *
     * @param string $role
     * @return CompaniesHouseOfficer
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return CompaniesHouseOfficer
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
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
