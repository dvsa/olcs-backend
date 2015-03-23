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
 *        @ORM\Index(name="ix_tm_employment_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_tm_employment_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_tm_employment_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_employment_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmEmployment implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Position45Field,
        Traits\CustomVersionField;

    /**
     * Employer name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="employer_name", length=90, nullable=true)
     */
    protected $employerName;

    /**
     * Hours per week
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hours_per_week", length=100, nullable=true)
     */
    protected $hoursPerWeek;

    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", inversedBy="employments")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

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
     * Set the hours per week
     *
     * @param string $hoursPerWeek
     * @return TmEmployment
     */
    public function setHoursPerWeek($hoursPerWeek)
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    /**
     * Get the hours per week
     *
     * @return string
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }

    /**
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return TmEmployment
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }
}
