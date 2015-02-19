<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="fk_complaint_contact_details1_idx", columns={"complainant_contact_details_id"}),
 *        @ORM\Index(name="fk_complaint_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_complaint_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_complaint_ref_data1_idx", columns={"status"}),
 *        @ORM\Index(name="fk_complaint_ref_data2_idx", columns={"complaint_type"}),
 *        @ORM\Index(name="fk_complaint_cases1_idx", columns={"case_id"})
 *    }
 * )
 */
class Complaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CloseDateField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\StatusManyToOneAlt1,
        Traits\CustomVersionField,
        Traits\Vrm20Field;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="complaints")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    protected $case;

    /**
     * Complainant contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", cascade={"persist"})
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
     * Complaint type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="complaint_type", referencedColumnName="id", nullable=true)
     */
    protected $complaintType;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=4000, nullable=true)
     */
    protected $description;

    /**
     * Driver family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="driver_family_name", length=40, nullable=true)
     */
    protected $driverFamilyName;

    /**
     * Driver forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="driver_forename", length=40, nullable=true)
     */
    protected $driverForename;

    /**
     * Is compliance
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_compliance", nullable=false, options={"default": 1})
     */
    protected $isCompliance;

    /**
     * Oc complaint
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OcComplaint", mappedBy="complaint")
     */
    protected $ocComplaints;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->ocComplaints = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Complaint
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
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
     * Set the is compliance
     *
     * @param boolean $isCompliance
     * @return Complaint
     */
    public function setIsCompliance($isCompliance)
    {
        $this->isCompliance = $isCompliance;

        return $this;
    }

    /**
     * Get the is compliance
     *
     * @return boolean
     */
    public function getIsCompliance()
    {
        return $this->isCompliance;
    }

    /**
     * Set the oc complaint
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return Complaint
     */
    public function setOcComplaints($ocComplaints)
    {
        $this->ocComplaints = $ocComplaints;

        return $this;
    }

    /**
     * Get the oc complaints
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOcComplaints()
    {
        return $this->ocComplaints;
    }

    /**
     * Add a oc complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return Complaint
     */
    public function addOcComplaints($ocComplaints)
    {
        if ($ocComplaints instanceof ArrayCollection) {
            $this->ocComplaints = new ArrayCollection(
                array_merge(
                    $this->ocComplaints->toArray(),
                    $ocComplaints->toArray()
                )
            );
        } elseif (!$this->ocComplaints->contains($ocComplaints)) {
            $this->ocComplaints->add($ocComplaints);
        }

        return $this;
    }

    /**
     * Remove a oc complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return Complaint
     */
    public function removeOcComplaints($ocComplaints)
    {
        if ($this->ocComplaints->contains($ocComplaints)) {
            $this->ocComplaints->removeElement($ocComplaints);
        }

        return $this;
    }
}
