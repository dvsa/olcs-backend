<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseOfficer Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_officer",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_officer_role", columns={"role"}),
 *        @ORM\Index(name="ix_companies_house_officer_companies_house_company_id", columns={"company_id"})
 *    }
 * )
 */
class CompaniesHouseOfficer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RoleManyToOne,
        Traits\CustomVersionField;

    /**
     * Company
     *
     * @var \Olcs\Db\Entity\CompaniesHouseCompany
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CompaniesHouseCompany")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     */
    protected $company;

    /**
     * Date of birth
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_of_birth", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=100, nullable=true)
     */
    protected $name;

    /**
     * Set the company
     *
     * @param \Olcs\Db\Entity\CompaniesHouseCompany $company
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
     * @return \Olcs\Db\Entity\CompaniesHouseCompany
     */
    public function getCompany()
    {
        return $this->company;
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
}
