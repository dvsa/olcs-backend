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
 *    }
 * )
 */
class CompanySubsidiaryLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Company subsidiary
     *
     * @var \Olcs\Db\Entity\CompanySubsidiary
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\CompanySubsidiary")
     * @ORM\JoinColumn(name="company_subsidiary_id", referencedColumnName="id")
     */
    protected $companySubsidiary;

    /**
     * Identifier - Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Licence")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id")
     */
    protected $licence;

    /**
     * Set the company subsidiary
     *
     * @param \Olcs\Db\Entity\CompanySubsidiary $companySubsidiary
     * @return \Olcs\Db\Entity\CompanySubsidiaryLicence
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
     * @return \Olcs\Db\Entity\CompanySubsidiaryLicence
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
