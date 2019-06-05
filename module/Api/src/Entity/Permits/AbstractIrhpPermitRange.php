<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermitRange Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_range",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_ranges_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"}),
 *        @ORM\Index(name="fk_irhp_permit_range_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_range_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uniqueRange", columns={"irhp_permit_stock_id","from_no","to_no"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitRange implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;

    /**
     * Country
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country",
     *     inversedBy="irhpPermitStockRanges",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="irhp_permit_range_country",
     *     joinColumns={
     *         @ORM\JoinColumn(name="irhp_permit_stock_range_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $countrys;

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
     * From no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="from_no", nullable=true)
     */
    protected $fromNo;

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
     * Irhp permit stock
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock",
     *     fetch="LAZY",
     *     inversedBy="irhpPermitRanges"
     * )
     * @ORM\JoinColumn(name="irhp_permit_stock_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitStock;

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
     * Lost replacement
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="lost_replacement", nullable=true)
     */
    protected $lostReplacement;

    /**
     * Prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="prefix", length=45, nullable=true)
     */
    protected $prefix;

    /**
     * Ss reserve
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="ss_reserve", nullable=true)
     */
    protected $ssReserve;

    /**
     * To no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="to_no", nullable=true)
     */
    protected $toNo;

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
     * Irhp candidate permit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit",
     *     mappedBy="irhpPermitRange"
     * )
     */
    protected $irhpCandidatePermits;

    /**
     * Irhp permit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermit",
     *     mappedBy="irhpPermitRange"
     * )
     */
    protected $irhpPermits;

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
        $this->countrys = new ArrayCollection();
        $this->irhpCandidatePermits = new ArrayCollection();
        $this->irhpPermits = new ArrayCollection();
    }

    /**
     * Set the country
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being set as the value
     *
     * @return IrhpPermitRange
     */
    public function setCountrys($countrys)
    {
        $this->countrys = $countrys;

        return $this;
    }

    /**
     * Get the countrys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCountrys()
    {
        return $this->countrys;
    }

    /**
     * Add a countrys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being added
     *
     * @return IrhpPermitRange
     */
    public function addCountrys($countrys)
    {
        if ($countrys instanceof ArrayCollection) {
            $this->countrys = new ArrayCollection(
                array_merge(
                    $this->countrys->toArray(),
                    $countrys->toArray()
                )
            );
        } elseif (!$this->countrys->contains($countrys)) {
            $this->countrys->add($countrys);
        }

        return $this;
    }

    /**
     * Remove a countrys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being removed
     *
     * @return IrhpPermitRange
     */
    public function removeCountrys($countrys)
    {
        if ($this->countrys->contains($countrys)) {
            $this->countrys->removeElement($countrys);
        }

        return $this;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpPermitRange
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
     * @return IrhpPermitRange
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
     * Set the from no
     *
     * @param int $fromNo new value being set
     *
     * @return IrhpPermitRange
     */
    public function setFromNo($fromNo)
    {
        $this->fromNo = $fromNo;

        return $this;
    }

    /**
     * Get the from no
     *
     * @return int
     */
    public function getFromNo()
    {
        return $this->fromNo;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermitRange
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
     * Set the irhp permit stock
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock $irhpPermitStock entity being set as the value
     *
     * @return IrhpPermitRange
     */
    public function setIrhpPermitStock($irhpPermitStock)
    {
        $this->irhpPermitStock = $irhpPermitStock;

        return $this;
    }

    /**
     * Get the irhp permit stock
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock
     */
    public function getIrhpPermitStock()
    {
        return $this->irhpPermitStock;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpPermitRange
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
     * @return IrhpPermitRange
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
     * Set the lost replacement
     *
     * @param boolean $lostReplacement new value being set
     *
     * @return IrhpPermitRange
     */
    public function setLostReplacement($lostReplacement)
    {
        $this->lostReplacement = $lostReplacement;

        return $this;
    }

    /**
     * Get the lost replacement
     *
     * @return boolean
     */
    public function getLostReplacement()
    {
        return $this->lostReplacement;
    }

    /**
     * Set the prefix
     *
     * @param string $prefix new value being set
     *
     * @return IrhpPermitRange
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the ss reserve
     *
     * @param boolean $ssReserve new value being set
     *
     * @return IrhpPermitRange
     */
    public function setSsReserve($ssReserve)
    {
        $this->ssReserve = $ssReserve;

        return $this;
    }

    /**
     * Get the ss reserve
     *
     * @return boolean
     */
    public function getSsReserve()
    {
        return $this->ssReserve;
    }

    /**
     * Set the to no
     *
     * @param int $toNo new value being set
     *
     * @return IrhpPermitRange
     */
    public function setToNo($toNo)
    {
        $this->toNo = $toNo;

        return $this;
    }

    /**
     * Get the to no
     *
     * @return int
     */
    public function getToNo()
    {
        return $this->toNo;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitRange
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
     * Set the irhp candidate permit
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being set as the value
     *
     * @return IrhpPermitRange
     */
    public function setIrhpCandidatePermits($irhpCandidatePermits)
    {
        $this->irhpCandidatePermits = $irhpCandidatePermits;

        return $this;
    }

    /**
     * Get the irhp candidate permits
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpCandidatePermits()
    {
        return $this->irhpCandidatePermits;
    }

    /**
     * Add a irhp candidate permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being added
     *
     * @return IrhpPermitRange
     */
    public function addIrhpCandidatePermits($irhpCandidatePermits)
    {
        if ($irhpCandidatePermits instanceof ArrayCollection) {
            $this->irhpCandidatePermits = new ArrayCollection(
                array_merge(
                    $this->irhpCandidatePermits->toArray(),
                    $irhpCandidatePermits->toArray()
                )
            );
        } elseif (!$this->irhpCandidatePermits->contains($irhpCandidatePermits)) {
            $this->irhpCandidatePermits->add($irhpCandidatePermits);
        }

        return $this;
    }

    /**
     * Remove a irhp candidate permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being removed
     *
     * @return IrhpPermitRange
     */
    public function removeIrhpCandidatePermits($irhpCandidatePermits)
    {
        if ($this->irhpCandidatePermits->contains($irhpCandidatePermits)) {
            $this->irhpCandidatePermits->removeElement($irhpCandidatePermits);
        }

        return $this;
    }

    /**
     * Set the irhp permit
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being set as the value
     *
     * @return IrhpPermitRange
     */
    public function setIrhpPermits($irhpPermits)
    {
        $this->irhpPermits = $irhpPermits;

        return $this;
    }

    /**
     * Get the irhp permits
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermits()
    {
        return $this->irhpPermits;
    }

    /**
     * Add a irhp permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being added
     *
     * @return IrhpPermitRange
     */
    public function addIrhpPermits($irhpPermits)
    {
        if ($irhpPermits instanceof ArrayCollection) {
            $this->irhpPermits = new ArrayCollection(
                array_merge(
                    $this->irhpPermits->toArray(),
                    $irhpPermits->toArray()
                )
            );
        } elseif (!$this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->add($irhpPermits);
        }

        return $this;
    }

    /**
     * Remove a irhp permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being removed
     *
     * @return IrhpPermitRange
     */
    public function removeIrhpPermits($irhpPermits)
    {
        if ($this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->removeElement($irhpPermits);
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
}
