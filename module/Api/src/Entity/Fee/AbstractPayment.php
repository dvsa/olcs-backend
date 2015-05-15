<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="payment",
 *    indexes={
 *        @ORM\Index(name="ix_payment_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_payment_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_payment_payment_status", columns={"status"})
 *    }
 * )
 */
abstract class AbstractPayment
{

    /**
     * Completed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="completed_date", nullable=true)
     */
    protected $completedDate;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
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
     * Guid
     *
     * @var string
     *
     * @ORM\Column(type="string", name="guid", length=255, nullable=true)
     */
    protected $guid;

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
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
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
     * Legacy choice
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="legacy_choice", nullable=true)
     */
    protected $legacyChoice;

    /**
     * Legacy guid
     *
     * @var string
     *
     * @ORM\Column(type="string", name="legacy_guid", length=255, nullable=true)
     */
    protected $legacyGuid;

    /**
     * Legacy method
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="legacy_method", nullable=true)
     */
    protected $legacyMethod;

    /**
     * Legacy status
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="legacy_status", nullable=true)
     */
    protected $legacyStatus;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the completed date
     *
     * @param \DateTime $completedDate
     * @return Payment
     */
    public function setCompletedDate($completedDate)
    {
        $this->completedDate = $completedDate;

        return $this;
    }

    /**
     * Get the completed date
     *
     * @return \DateTime
     */
    public function getCompletedDate()
    {
        return $this->completedDate;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Payment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return Payment
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the guid
     *
     * @param string $guid
     * @return Payment
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get the guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Payment
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Payment
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return Payment
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the legacy choice
     *
     * @param int $legacyChoice
     * @return Payment
     */
    public function setLegacyChoice($legacyChoice)
    {
        $this->legacyChoice = $legacyChoice;

        return $this;
    }

    /**
     * Get the legacy choice
     *
     * @return int
     */
    public function getLegacyChoice()
    {
        return $this->legacyChoice;
    }

    /**
     * Set the legacy guid
     *
     * @param string $legacyGuid
     * @return Payment
     */
    public function setLegacyGuid($legacyGuid)
    {
        $this->legacyGuid = $legacyGuid;

        return $this;
    }

    /**
     * Get the legacy guid
     *
     * @return string
     */
    public function getLegacyGuid()
    {
        return $this->legacyGuid;
    }

    /**
     * Set the legacy method
     *
     * @param int $legacyMethod
     * @return Payment
     */
    public function setLegacyMethod($legacyMethod)
    {
        $this->legacyMethod = $legacyMethod;

        return $this;
    }

    /**
     * Get the legacy method
     *
     * @return int
     */
    public function getLegacyMethod()
    {
        return $this->legacyMethod;
    }

    /**
     * Set the legacy status
     *
     * @param int $legacyStatus
     * @return Payment
     */
    public function setLegacyStatus($legacyStatus)
    {
        $this->legacyStatus = $legacyStatus;

        return $this;
    }

    /**
     * Get the legacy status
     *
     * @return int
     */
    public function getLegacyStatus()
    {
        return $this->legacyStatus;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return Payment
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
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
