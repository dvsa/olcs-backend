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
 *        @ORM\Index(name="fk_complaint_contact_details1_idx", columns={"complainant_contact_details_id"}),
 *        @ORM\Index(name="fk_complaint_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_complaint_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_complaint_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_complaint_ref_data1_idx", columns={"status"}),
 *        @ORM\Index(name="fk_complaint_ref_data2_idx", columns={"complaint_type"}),
 *        @ORM\Index(name="fk_complaint_driver1_idx", columns={"driver_id"})
 *    }
 * )
 */
class Complaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OrganisationManyToOne,
        Traits\Description4000Field,
        Traits\Vrm20Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Driver
     *
     * @var \Olcs\Db\Entity\Driver
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Driver", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id", nullable=true)
     */
    protected $driver;

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
