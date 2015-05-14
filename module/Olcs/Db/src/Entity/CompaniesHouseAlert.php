<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseAlert Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_alert",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_organisation_id", columns={"organisation_id"})
 *    }
 * )
 */
class CompaniesHouseAlert implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CompanyOrLlpNo20Field,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\CustomLastModifiedOnField,
        Traits\Name160Field,
        Traits\OrganisationManyToOne,
        Traits\CustomVersionField;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="last_modified_by", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Set the created by
     *
     * @param int $createdBy
     * @return CompaniesHouseAlert
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy
     * @return CompaniesHouseAlert
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return int
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }
}
