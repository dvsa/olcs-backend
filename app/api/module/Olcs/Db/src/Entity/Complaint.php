<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Complaint Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="complaint",
 *    indexes={
 *        @ORM\Index(name="IDX_5F2732B57B00651C", columns={"status"}),
 *        @ORM\Index(name="IDX_5F2732B553DF8182", columns={"complaint_type"}),
 *        @ORM\Index(name="IDX_5F2732B5DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5F2732B565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_5F2732B5CF10D4F5", columns={"case_id"})
 *    }
 * )
 */
class Complaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Vrm20Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Complaint type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="complaint_type", referencedColumnName="id", nullable=true)
     */
    protected $complaintType;

    /**
     * Complaint date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="complaint_date", nullable=true)
     */
    protected $complaintDate;

    /**
     * Complainant forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="complainant_forename", length=40, nullable=true)
     */
    protected $complainantForename;

    /**
     * Complainant family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="complainant_family_name", length=40, nullable=true)
     */
    protected $complainantFamilyName;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=4000, nullable=true)
     */
    protected $description;

    /**
     * Driver forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="driver_forename", length=40, nullable=true)
     */
    protected $driverForename;

    /**
     * Driver family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="driver_family_name", length=40, nullable=true)
     */
    protected $driverFamilyName;

    /**
     * Set the status
     *
     * @param \Olcs\Db\Entity\RefData $status
     * @return Complaint
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the complaint type
     *
     * @param \Olcs\Db\Entity\RefData $complaintType
     * @return Complaint
     */
    public function setComplaintType($complaintType)
    {
        $this->complaintType = $complaintType;

        return $this;
    }

    /**
     * Get the complaint type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getComplaintType()
    {
        return $this->complaintType;
    }

    /**
     * Set the complaint date
     *
     * @param \DateTime $complaintDate
     * @return Complaint
     */
    public function setComplaintDate($complaintDate)
    {
        $this->complaintDate = $complaintDate;

        return $this;
    }

    /**
     * Get the complaint date
     *
     * @return \DateTime
     */
    public function getComplaintDate()
    {
        return $this->complaintDate;
    }

    /**
     * Set the complainant forename
     *
     * @param string $complainantForename
     * @return Complaint
     */
    public function setComplainantForename($complainantForename)
    {
        $this->complainantForename = $complainantForename;

        return $this;
    }

    /**
     * Get the complainant forename
     *
     * @return string
     */
    public function getComplainantForename()
    {
        return $this->complainantForename;
    }

    /**
     * Set the complainant family name
     *
     * @param string $complainantFamilyName
     * @return Complaint
     */
    public function setComplainantFamilyName($complainantFamilyName)
    {
        $this->complainantFamilyName = $complainantFamilyName;

        return $this;
    }

    /**
     * Get the complainant family name
     *
     * @return string
     */
    public function getComplainantFamilyName()
    {
        return $this->complainantFamilyName;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Complaint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the driver forename
     *
     * @param string $driverForename
     * @return Complaint
     */
    public function setDriverForename($driverForename)
    {
        $this->driverForename = $driverForename;

        return $this;
    }

    /**
     * Get the driver forename
     *
     * @return string
     */
    public function getDriverForename()
    {
        return $this->driverForename;
    }

    /**
     * Set the driver family name
     *
     * @param string $driverFamilyName
     * @return Complaint
     */
    public function setDriverFamilyName($driverFamilyName)
    {
        $this->driverFamilyName = $driverFamilyName;

        return $this;
    }

    /**
     * Get the driver family name
     *
     * @return string
     */
    public function getDriverFamilyName()
    {
        return $this->driverFamilyName;
    }
}
