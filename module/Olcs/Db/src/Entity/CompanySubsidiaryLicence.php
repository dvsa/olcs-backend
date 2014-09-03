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
 *        @ORM\Index(name="fk_company_subsidiary_has_licence_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_company_subsidiary_has_licence_company_subsidiary1_idx", columns={"company_subsidiary_id"}),
 *        @ORM\Index(name="fk_company_subsidiary_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_company_subsidiary_licence_user2_idx", columns={"last_modified_by"})
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
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOneAlt1,
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
