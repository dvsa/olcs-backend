<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApplicationStatus Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_status",
 *    indexes={
 *        @ORM\Index(name="ecmt_application_status_created_by", columns={"created_by"})
 *    }
 * )
 */
abstract class AbstractApplicationStatus implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Identifier - Application status id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="application_status_id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $applicationStatusId;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=false)
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
     * Status name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status_name", length=10, nullable=false)
     */
    protected $statusName;

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
     * Set the application status id
     *
     * @param int $applicationStatusId new value being set
     *
     * @return ApplicationStatus
     */
    public function setApplicationStatusId($applicationStatusId)
    {
        $this->applicationStatusId = $applicationStatusId;

        return $this;
    }

    /**
     * Get the application status id
     *
     * @return int
     */
    public function getApplicationStatusId()
    {
        return $this->applicationStatusId;
    }

    /**
     * Set the created by
     *
     * @param int $createdBy new value being set
     *
     * @return ApplicationStatus
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
     * @return ApplicationStatus
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
     * Set the last modified by
     *
     * @param int $lastModifiedBy new value being set
     *
     * @return ApplicationStatus
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
     * @return ApplicationStatus
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
     * Set the status name
     *
     * @param string $statusName new value being set
     *
     * @return ApplicationStatus
     */
    public function setStatusName($statusName)
    {
        $this->statusName = $statusName;

        return $this;
    }

    /**
     * Get the status name
     *
     * @return string
     */
    public function getStatusName()
    {
        return $this->statusName;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ApplicationStatus
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
