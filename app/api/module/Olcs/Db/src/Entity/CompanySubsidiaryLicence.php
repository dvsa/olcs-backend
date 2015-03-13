<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompanySubsidiaryLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="company_subsidiary_licence",
 *    indexes={
 *        @ORM\Index(name="ix_company_subsidiary_licence_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_company_subsidiary_licence_company_subsidiary_id", columns={"company_subsidiary_id"}),
 *        @ORM\Index(name="ix_company_subsidiary_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_company_subsidiary_licence_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_company_subsidiary_licence_company_subsidiary_id_licence_id", columns={"company_subsidiary_id","licence_id"})
 *    }
 * )
 */
class CompanySubsidiaryLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Company subsidiary
     *
     * @var \Olcs\Db\Entity\CompanySubsidiary
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CompanySubsidiary")
     * @ORM\JoinColumn(name="company_subsidiary_id", referencedColumnName="id", nullable=false)
     */
    protected $companySubsidiary;

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
     * Set the company subsidiary
     *
     * @param \Olcs\Db\Entity\CompanySubsidiary $companySubsidiary
     * @return CompanySubsidiaryLicence
     */
    public function setCompanySubsidiary($companySubsidiary)
    {
        $this->companySubsidiary = $companySubsidiary;

        return $this;
    }

    /**
     * Get the company subsidiary
     *
     * @return \Olcs\Db\Entity\CompanySubsidiary
     */
    public function getCompanySubsidiary()
    {
        return $this->companySubsidiary;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return CompanySubsidiaryLicence
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
}
