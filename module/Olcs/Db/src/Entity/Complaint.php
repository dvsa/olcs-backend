<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Complaint Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="complaint",
 *    indexes={
 *        @ORM\Index(name="IDX_5F2732B5C97DA9B9", columns={"complainant_contact_details_id"}),
 *        @ORM\Index(name="IDX_5F2732B5C3423909", columns={"driver_id"}),
 *        @ORM\Index(name="IDX_5F2732B57B00651C", columns={"status"}),
 *        @ORM\Index(name="IDX_5F2732B553DF8182", columns={"complaint_type"}),
 *        @ORM\Index(name="IDX_5F2732B5DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5F2732B59E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_5F2732B565CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Complaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\OrganisationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Description4000Field,
        Traits\Vrm20Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Complainant contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="complainant_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $complainantContactDetails;

    /**
     * Driver
     *
     * @var \Olcs\Db\Entity\Driver
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Driver", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id", nullable=true)
     */
    protected $driver;

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
     * Status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Complaint date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="complaint_date", nullable=true)
     */
    protected $complaintDate;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column(type="string", name="value", length=8, nullable=true)
     */
    protected $value;

    /**
     * Set the complainant contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $complainantContactDetails
     * @return Complaint
     */
    public function setComplainantContactDetails($complainantContactDetails)
    {
        $this->complainantContactDetails = $complainantContactDetails;

        return $this;
    }

    /**
     * Get the complainant contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getComplainantContactDetails()
    {
        return $this->complainantContactDetails;
    }

    /**
     * Set the driver
     *
     * @param \Olcs\Db\Entity\Driver $driver
     * @return Complaint
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get the driver
     *
     * @return \Olcs\Db\Entity\Driver
     */
    public function getDriver()
    {
        return $this->driver;
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
     * Set the value
     *
     * @param string $value
     * @return Complaint
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
