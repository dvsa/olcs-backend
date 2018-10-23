<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermitStock Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_stock",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_irhp_permit_types1_idx",
     *     columns={"irhp_permit_type_id"}),
 *        @ORM\Index(name="fk_irhp_permit_stock_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_stock_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irhp_permit_stock_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uniqueStock", columns={"irhp_permit_type_id","valid_from","valid_to"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitStock implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
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
     * Initial stock
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="initial_stock", nullable=true)
     */
    protected $initialStock;

    /**
     * Irhp permit type
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitType;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
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
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Valid from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="valid_from", nullable=true)
     */
    protected $validFrom;

    /**
     * Valid to
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="valid_to", nullable=true)
     */
    protected $validTo;

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
     * Irhp permit range
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange",
     *     mappedBy="irhpPermitStock"
     * )
     */
    protected $irhpPermitRanges;

    /**
     * Irhp permit sector quota
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitSectorQuota",
     *     mappedBy="irhpPermitStock"
     * )
     */
    protected $irhpPermitSectorQuotas;

    /**
     * Irhp permit window
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow",
     *     mappedBy="irhpPermitStock"
     * )
     */
    protected $irhpPermitWindows;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->irhpPermitRanges = new ArrayCollection();
        $this->irhpPermitSectorQuotas = new ArrayCollection();
        $this->irhpPermitWindows = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpPermitStock
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
     * @param \DateTime $createdOn new value being set
     *
     * @return IrhpPermitStock
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
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermitStock
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
     * Set the initial stock
     *
     * @param int $initialStock new value being set
     *
     * @return IrhpPermitStock
     */
    public function setInitialStock($initialStock)
    {
        $this->initialStock = $initialStock;

        return $this;
    }

    /**
     * Get the initial stock
     *
     * @return int
     */
    public function getInitialStock()
    {
        return $this->initialStock;
    }

    /**
     * Set the irhp permit type
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType $irhpPermitType entity being set as the value
     *
     * @return IrhpPermitStock
     */
    public function setIrhpPermitType($irhpPermitType)
    {
        $this->irhpPermitType = $irhpPermitType;

        return $this;
    }

    /**
     * Get the irhp permit type
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType
     */
    public function getIrhpPermitType()
    {
        return $this->irhpPermitType;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpPermitStock
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
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return IrhpPermitStock
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
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return IrhpPermitStock
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
     * Set the valid from
     *
     * @param \DateTime $validFrom new value being set
     *
     * @return IrhpPermitStock
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get the valid from
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getValidFrom($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->validFrom);
        }

        return $this->validFrom;
    }

    /**
     * Set the valid to
     *
     * @param \DateTime $validTo new value being set
     *
     * @return IrhpPermitStock
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get the valid to
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getValidTo($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->validTo);
        }

        return $this->validTo;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitStock
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
     * Set the irhp permit range
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitRanges collection being set as the value
     *
     * @return IrhpPermitStock
     */
    public function setIrhpPermitRanges($irhpPermitRanges)
    {
        $this->irhpPermitRanges = $irhpPermitRanges;

        return $this;
    }

    /**
     * Get the irhp permit ranges
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitRanges()
    {
        return $this->irhpPermitRanges;
    }

    /**
     * Add a irhp permit ranges
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitRanges collection being added
     *
     * @return IrhpPermitStock
     */
    public function addIrhpPermitRanges($irhpPermitRanges)
    {
        if ($irhpPermitRanges instanceof ArrayCollection) {
            $this->irhpPermitRanges = new ArrayCollection(
                array_merge(
                    $this->irhpPermitRanges->toArray(),
                    $irhpPermitRanges->toArray()
                )
            );
        } elseif (!$this->irhpPermitRanges->contains($irhpPermitRanges)) {
            $this->irhpPermitRanges->add($irhpPermitRanges);
        }

        return $this;
    }

    /**
     * Remove a irhp permit ranges
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitRanges collection being removed
     *
     * @return IrhpPermitStock
     */
    public function removeIrhpPermitRanges($irhpPermitRanges)
    {
        if ($this->irhpPermitRanges->contains($irhpPermitRanges)) {
            $this->irhpPermitRanges->removeElement($irhpPermitRanges);
        }

        return $this;
    }

    /**
     * Set the irhp permit sector quota
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitSectorQuotas collection being set as the value
     *
     * @return IrhpPermitStock
     */
    public function setIrhpPermitSectorQuotas($irhpPermitSectorQuotas)
    {
        $this->irhpPermitSectorQuotas = $irhpPermitSectorQuotas;

        return $this;
    }

    /**
     * Get the irhp permit sector quotas
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitSectorQuotas()
    {
        return $this->irhpPermitSectorQuotas;
    }

    /**
     * Add a irhp permit sector quotas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitSectorQuotas collection being added
     *
     * @return IrhpPermitStock
     */
    public function addIrhpPermitSectorQuotas($irhpPermitSectorQuotas)
    {
        if ($irhpPermitSectorQuotas instanceof ArrayCollection) {
            $this->irhpPermitSectorQuotas = new ArrayCollection(
                array_merge(
                    $this->irhpPermitSectorQuotas->toArray(),
                    $irhpPermitSectorQuotas->toArray()
                )
            );
        } elseif (!$this->irhpPermitSectorQuotas->contains($irhpPermitSectorQuotas)) {
            $this->irhpPermitSectorQuotas->add($irhpPermitSectorQuotas);
        }

        return $this;
    }

    /**
     * Remove a irhp permit sector quotas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitSectorQuotas collection being removed
     *
     * @return IrhpPermitStock
     */
    public function removeIrhpPermitSectorQuotas($irhpPermitSectorQuotas)
    {
        if ($this->irhpPermitSectorQuotas->contains($irhpPermitSectorQuotas)) {
            $this->irhpPermitSectorQuotas->removeElement($irhpPermitSectorQuotas);
        }

        return $this;
    }

    /**
     * Set the irhp permit window
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitWindows collection being set as the value
     *
     * @return IrhpPermitStock
     */
    public function setIrhpPermitWindows($irhpPermitWindows)
    {
        $this->irhpPermitWindows = $irhpPermitWindows;

        return $this;
    }

    /**
     * Get the irhp permit windows
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitWindows()
    {
        return $this->irhpPermitWindows;
    }

    /**
     * Add a irhp permit windows
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitWindows collection being added
     *
     * @return IrhpPermitStock
     */
    public function addIrhpPermitWindows($irhpPermitWindows)
    {
        if ($irhpPermitWindows instanceof ArrayCollection) {
            $this->irhpPermitWindows = new ArrayCollection(
                array_merge(
                    $this->irhpPermitWindows->toArray(),
                    $irhpPermitWindows->toArray()
                )
            );
        } elseif (!$this->irhpPermitWindows->contains($irhpPermitWindows)) {
            $this->irhpPermitWindows->add($irhpPermitWindows);
        }

        return $this;
    }

    /**
     * Remove a irhp permit windows
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitWindows collection being removed
     *
     * @return IrhpPermitStock
     */
    public function removeIrhpPermitWindows($irhpPermitWindows)
    {
        if ($this->irhpPermitWindows->contains($irhpPermitWindows)) {
            $this->irhpPermitWindows->removeElement($irhpPermitWindows);
        }

        return $this;
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
                if ($this->$property instanceof Collection) {
                    $this->$property = new ArrayCollection(array());
                } else {
                    $this->$property = null;
                }
            }
        }
    }
}
