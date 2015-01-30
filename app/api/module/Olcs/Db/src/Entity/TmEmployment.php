<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmEmployment Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_employment",
 *    indexes={
 *        @ORM\Index(name="fk_tm_employment_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_tm_employment_contact_details1_idx", columns={"contact_details_id"})
 *    }
 * )
 */
class TmEmployment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOneAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\HoursPerWeekField,
        Traits\IdIdentity,
        Traits\CustomLastModifiedOnField,
        Traits\Position45Field,
        Traits\TransportManagerManyToOneAlt1,
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
     * Employer name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="employer_name", length=90, nullable=true)
     */
    protected $employerName;

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
     * @return TmEmployment
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
     * Set the employer name
     *
     * @param string $employerName
     * @return TmEmployment
     */
    public function setEmployerName($employerName)
    {
        $this->employerName = $employerName;

        return $this;
    }

    /**
     * Get the employer name
     *
     * @return string
     */
    public function getEmployerName()
    {
        return $this->employerName;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy
     * @return TmEmployment
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
