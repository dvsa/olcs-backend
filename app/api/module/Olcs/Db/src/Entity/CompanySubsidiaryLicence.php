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
 *        @ORM\Index(name="IDX_A4097CA1A8859FDF", columns={"company_subsidiary_id"}),
 *        @ORM\Index(name="IDX_A4097CA1DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_A4097CA165CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_A4097CA126EF07C9", columns={"licence_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="company_subsid_licence_unique", columns={"company_subsidiary_id","licence_id"})
 *    }
 * )
 */
class CompanySubsidiaryLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Company subsidiary
     *
     * @var \Olcs\Db\Entity\CompanySubsidiary
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CompanySubsidiary", fetch="LAZY")
     * @ORM\JoinColumn(name="company_subsidiary_id", referencedColumnName="id", nullable=false)
     */
    protected $companySubsidiary;

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
}
