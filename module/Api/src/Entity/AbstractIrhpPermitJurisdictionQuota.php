<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermitJurisdictionQuota Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_jurisdiction_quota",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_jurisdiction_quotas_irhp_jurisdictions1_idx",
     *     columns={"irhp_jurisdiction_id"}),
 *        @ORM\Index(name="fk_irhp_jurisdiction_quotas_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitJurisdictionQuota implements BundleSerializableInterface, JsonSerializable
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
     * Irhp jurisdiction
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpJurisdiction
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpJurisdiction", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_jurisdiction_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpJurisdiction;

    /**
     * Irhp permit stock
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpPermitStock
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpPermitStock", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_stock_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitStock;

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
     * Quota number
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="quota_number", nullable=true)
     */
    protected $quotaNumber;

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
     * @return IrhpPermitJurisdictionQuota
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
     * @return IrhpPermitJurisdictionQuota
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
     * @return IrhpPermitJurisdictionQuota
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
     * Set the irhp jurisdiction
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpJurisdiction $irhpJurisdiction entity being set as the value
     *
     * @return IrhpPermitJurisdictionQuota
     */
    public function setIrhpJurisdiction($irhpJurisdiction)
    {
        $this->irhpJurisdiction = $irhpJurisdiction;

        return $this;
    }

    /**
     * Get the irhp jurisdiction
     *
     * @return \Dvsa\Olcs\Api\Entity\IrhpJurisdiction
     */
    public function getIrhpJurisdiction()
    {
        return $this->irhpJurisdiction;
    }

    /**
     * Set the irhp permit stock
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpPermitStock $irhpPermitStock entity being set as the value
     *
     * @return IrhpPermitJurisdictionQuota
     */
    public function setIrhpPermitStock($irhpPermitStock)
    {
        $this->irhpPermitStock = $irhpPermitStock;

        return $this;
    }

    /**
     * Get the irhp permit stock
     *
     * @return \Dvsa\Olcs\Api\Entity\IrhpPermitStock
     */
    public function getIrhpPermitStock()
    {
        return $this->irhpPermitStock;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy new value being set
     *
     * @return IrhpPermitJurisdictionQuota
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
     * @return IrhpPermitJurisdictionQuota
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
     * Set the quota number
     *
     * @param int $quotaNumber new value being set
     *
     * @return IrhpPermitJurisdictionQuota
     */
    public function setQuotaNumber($quotaNumber)
    {
        $this->quotaNumber = $quotaNumber;

        return $this;
    }

    /**
     * Get the quota number
     *
     * @return int
     */
    public function getQuotaNumber()
    {
        return $this->quotaNumber;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitJurisdictionQuota
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
