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
 *        @ORM\Index(name="ix_company_subsidiary_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_company_subsidiary_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_company_subsidiary_licence_id", columns={"licence_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_company_subsidiary_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CompanySubsidiary implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Company no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="company_no", length=12, nullable=true)
     */
    protected $companyNo;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="companySubsidiaries")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=true)
     */
    protected $name;

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

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return CompanySubsidiary
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

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
}
