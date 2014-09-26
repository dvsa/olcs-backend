<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompanySubsidiary Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="company_subsidiary",
 *    indexes={
 *        @ORM\Index(name="IDX_CEA210FCDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_CEA210FC65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class CompanySubsidiary implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=true)
     */
    protected $name;

    /**
     * Company no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_no", length=12, nullable=true)
     */
    protected $companyNo;

    /**
     * Set the name
     *
     * @param string $name
     * @return CompanySubsidiary
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
     * Set the company no
     *
     * @param string $companyNo
     * @return CompanySubsidiary
     */
    public function setCompanyNo($companyNo)
    {
        $this->companyNo = $companyNo;

        return $this;
    }

    /**
     * Get the company no
     *
     * @return string
     */
    public function getCompanyNo()
    {
        return $this->companyNo;
    }
}
