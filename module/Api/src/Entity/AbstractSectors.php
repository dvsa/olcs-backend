<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Sectors Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="sectors",
 *    indexes={
 *        @ORM\Index(name="ecmt_sectors_created_by", columns={"created_by"})
 *    }
 * )
 */
abstract class AbstractSectors implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
     * Sector abbr name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sector_abbr_name", length=10, nullable=false)
     */
    protected $sectorAbbrName;

    /**
     * Identifier - Sector id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="sector_id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $sectorId;

    /**
     * Sector name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sector_name", length=32, nullable=false)
     */
    protected $sectorName;

    /**
     * Sifting percentage
     *
     * @var unknown
     *
     * @ORM\Column(type="float", name="sifting_percentage", precision=6, scale=4, nullable=true)
     */
    protected $siftingPercentage;

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
     * @return Sectors
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
     * @return Sectors
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
     * Set the deleted date
     *
     * @param \DateTime $deletedDate new value being set
     *
     * @return Sectors
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDeletedDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->deletedDate);
        }

        return $this->deletedDate;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy new value being set
     *
     * @return Sectors
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
     * @return Sectors
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
     * Set the sector abbr name
     *
     * @param string $sectorAbbrName new value being set
     *
     * @return Sectors
     */
    public function setSectorAbbrName($sectorAbbrName)
    {
        $this->sectorAbbrName = $sectorAbbrName;

        return $this;
    }

    /**
     * Get the sector abbr name
     *
     * @return string
     */
    public function getSectorAbbrName()
    {
        return $this->sectorAbbrName;
    }

    /**
     * Set the sector id
     *
     * @param int $sectorId new value being set
     *
     * @return Sectors
     */
    public function setSectorId($sectorId)
    {
        $this->sectorId = $sectorId;

        return $this;
    }

    /**
     * Get the sector id
     *
     * @return int
     */
    public function getSectorId()
    {
        return $this->sectorId;
    }

    /**
     * Set the sector name
     *
     * @param string $sectorName new value being set
     *
     * @return Sectors
     */
    public function setSectorName($sectorName)
    {
        $this->sectorName = $sectorName;

        return $this;
    }

    /**
     * Get the sector name
     *
     * @return string
     */
    public function getSectorName()
    {
        return $this->sectorName;
    }

    /**
     * Set the sifting percentage
     *
     * @param unknown $siftingPercentage new value being set
     *
     * @return Sectors
     */
    public function setSiftingPercentage($siftingPercentage)
    {
        $this->siftingPercentage = $siftingPercentage;

        return $this;
    }

    /**
     * Get the sifting percentage
     *
     * @return unknown
     */
    public function getSiftingPercentage()
    {
        return $this->siftingPercentage;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Sectors
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
