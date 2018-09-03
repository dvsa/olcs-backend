<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_range_idx", columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_application1_idx",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_candidate_permit1_idx",
     *     columns={"irhp_candidate_permit_id"})
 *    }
 * )
 */
abstract class AbstractIrhpPermit implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=true)
     * @Gedmo\Blameable(on="create")
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="expiry_date", nullable=true)
     */
    protected $expiryDate;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Irhp candidate permit
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpCandidatePermit
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpCandidatePermit", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_candidate_permit_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpCandidatePermit;

    /**
     * Irhp permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpPermitApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpPermitApplication", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_application_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitApplication;

    /**
     * Irhp permit range
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpPermitRange
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpPermitRange", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_range_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitRange;

    /**
     * Issue date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="issue_date", nullable=true)
     */
    protected $issueDate;

    /**
     * Last modified by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="last_modified_by", nullable=true)
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Permit number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="permit_number", length=100, nullable=true)
     */
    protected $permitNumber;

    /**
     * Permit properties
     *
     * @var unknown
     *
     * @ORM\Column(type="json", name="permit_properties", nullable=true)
     */
    protected $permitProperties;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status", length=32, nullable=true)
     */
    protected $status;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=true)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the created by
     *
     * @param int $createdBy new value being set
     *
     * @return IrhpPermit
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
     * Set the created on
     *
     * @param \DateTime $createdOn new value being set
     *
     * @return IrhpPermit
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate new value being set
     *
     * @return IrhpPermit
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getExpiryDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->expiryDate);
        }

        return $this->expiryDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermit
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the irhp candidate permit
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpCandidatePermit $irhpCandidatePermit entity being set as the value
     *
     * @return IrhpPermit
     */
    public function setIrhpCandidatePermit($irhpCandidatePermit)
    {
        $this->irhpCandidatePermit = $irhpCandidatePermit;

        return $this;
    }

    /**
     * Get the irhp candidate permit
     *
     * @return \Dvsa\Olcs\Api\Entity\IrhpCandidatePermit
     */
    public function getIrhpCandidatePermit()
    {
        return $this->irhpCandidatePermit;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpPermitApplication $irhpPermitApplication entity being set as the value
     *
     * @return IrhpPermit
     */
    public function setIrhpPermitApplication($irhpPermitApplication)
    {
        $this->irhpPermitApplication = $irhpPermitApplication;

        return $this;
    }

    /**
     * Get the irhp permit application
     *
     * @return \Dvsa\Olcs\Api\Entity\IrhpPermitApplication
     */
    public function getIrhpPermitApplication()
    {
        return $this->irhpPermitApplication;
    }

    /**
     * Set the irhp permit range
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpPermitRange $irhpPermitRange entity being set as the value
     *
     * @return IrhpPermit
     */
    public function setIrhpPermitRange($irhpPermitRange)
    {
        $this->irhpPermitRange = $irhpPermitRange;

        return $this;
    }

    /**
     * Get the irhp permit range
     *
     * @return \Dvsa\Olcs\Api\Entity\IrhpPermitRange
     */
    public function getIrhpPermitRange()
    {
        return $this->irhpPermitRange;
    }

    /**
     * Set the issue date
     *
     * @param \DateTime $issueDate new value being set
     *
     * @return IrhpPermit
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * Get the issue date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getIssueDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->issueDate);
        }

        return $this->issueDate;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy new value being set
     *
     * @return IrhpPermit
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

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return IrhpPermit
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModifiedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModifiedOn);
        }

        return $this->lastModifiedOn;
    }

    /**
     * Set the permit number
     *
     * @param string $permitNumber new value being set
     *
     * @return IrhpPermit
     */
    public function setPermitNumber($permitNumber)
    {
        $this->permitNumber = $permitNumber;

        return $this;
    }

    /**
     * Get the permit number
     *
     * @return string
     */
    public function getPermitNumber()
    {
        return $this->permitNumber;
    }

    /**
     * Set the permit properties
     *
     * @param unknown $permitProperties new value being set
     *
     * @return IrhpPermit
     */
    public function setPermitProperties($permitProperties)
    {
        $this->permitProperties = $permitProperties;

        return $this;
    }

    /**
     * Get the permit properties
     *
     * @return unknown
     */
    public function getPermitProperties()
    {
        return $this->permitProperties;
    }

    /**
     * Set the status
     *
     * @param string $status new value being set
     *
     * @return IrhpPermit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermit
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
