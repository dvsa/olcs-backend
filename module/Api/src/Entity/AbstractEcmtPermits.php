<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EcmtPermits Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ecmt_permits",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permits_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permits_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permits_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permits_ecmt_permits_application_id",
     *     columns={"ecmt_permits_application_id"})
 *    }
 * )
 */
abstract class AbstractEcmtPermits implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Country
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country",
     *     mappedBy="ecmtPermits",
     *     fetch="LAZY"
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
     * Ecmt permits application
     *
     * @var \Dvsa\Olcs\Api\Entity\EcmtPermitApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\EcmtPermitApplication", fetch="LAZY")
     * @ORM\JoinColumn(name="ecmt_permits_application_id", referencedColumnName="id", nullable=false)
     */
    protected $ecmtPermitsApplication;

    /**
     * Expiry date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="expiry_date", nullable=true)
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
     * In force date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="in_force_date", nullable=true)
     */
    protected $inForceDate;

    /**
     * Intensity
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="intensity", nullable=false)
     */
    protected $intensity;

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
     * Payment status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="payment_status", referencedColumnName="id", nullable=false)
     */
    protected $paymentStatus;

    /**
     * Sector
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Sectors",
     *     mappedBy="ecmtPermits",
     *     fetch="LAZY"
     * )
     */
    protected $sectors;

    /**
     * Sifting random factor
     *
     * @var unknown
     *
     * @ORM\Column(type="float", name="sifting_random_factor", precision=6, scale=4, nullable=true)
     */
    protected $siftingRandomFactor;

    /**
     * Sifting value
     *
     * @var unknown
     *
     * @ORM\Column(type="float", name="sifting_value", precision=6, scale=4, nullable=true)
     */
    protected $siftingValue;

    /**
     * Sifting value random
     *
     * @var unknown
     *
     * @ORM\Column(type="float", name="sifting_value_random", precision=6, scale=4, nullable=true)
     */
    protected $siftingValueRandom;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
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
        $this->sectors = new ArrayCollection();
    }

    /**
     * Set the country
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being set as the value
     *
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * Set the ecmt permits application
     *
     * @param \Dvsa\Olcs\Api\Entity\EcmtPermitApplication $ecmtPermitsApplication entity being set as the value
     *
     * @return EcmtPermits
     */
    public function setEcmtPermitsApplication($ecmtPermitsApplication)
    {
        $this->ecmtPermitsApplication = $ecmtPermitsApplication;

        return $this;
    }

    /**
     * Get the ecmt permits application
     *
     * @return \Dvsa\Olcs\Api\Entity\EcmtPermitApplication
     */
    public function getEcmtPermitsApplication()
    {
        return $this->ecmtPermitsApplication;
    }

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate new value being set
     *
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * Set the in force date
     *
     * @param \DateTime $inForceDate new value being set
     *
     * @return EcmtPermits
     */
    public function setInForceDate($inForceDate)
    {
        $this->inForceDate = $inForceDate;

        return $this;
    }

    /**
     * Get the in force date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getInForceDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->inForceDate);
        }

        return $this->inForceDate;
    }

    /**
     * Set the intensity
     *
     * @param int $intensity new value being set
     *
     * @return EcmtPermits
     */
    public function setIntensity($intensity)
    {
        $this->intensity = $intensity;

        return $this;
    }

    /**
     * Get the intensity
     *
     * @return int
     */
    public function getIntensity()
    {
        return $this->intensity;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return EcmtPermits
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
     * @return EcmtPermits
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
     * Set the payment status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $paymentStatus entity being set as the value
     *
     * @return EcmtPermits
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get the payment status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set the sector
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sectors collection being set as the value
     *
     * @return EcmtPermits
     */
    public function setSectors($sectors)
    {
        $this->sectors = $sectors;

        return $this;
    }

    /**
     * Get the sectors
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSectors()
    {
        return $this->sectors;
    }

    /**
     * Add a sectors
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sectors collection being added
     *
     * @return EcmtPermits
     */
    public function addSectors($sectors)
    {
        if ($sectors instanceof ArrayCollection) {
            $this->sectors = new ArrayCollection(
                array_merge(
                    $this->sectors->toArray(),
                    $sectors->toArray()
                )
            );
        } elseif (!$this->sectors->contains($sectors)) {
            $this->sectors->add($sectors);
        }

        return $this;
    }

    /**
     * Remove a sectors
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sectors collection being removed
     *
     * @return EcmtPermits
     */
    public function removeSectors($sectors)
    {
        if ($this->sectors->contains($sectors)) {
            $this->sectors->removeElement($sectors);
        }

        return $this;
    }

    /**
     * Set the sifting random factor
     *
     * @param unknown $siftingRandomFactor new value being set
     *
     * @return EcmtPermits
     */
    public function setSiftingRandomFactor($siftingRandomFactor)
    {
        $this->siftingRandomFactor = $siftingRandomFactor;

        return $this;
    }

    /**
     * Get the sifting random factor
     *
     * @return unknown
     */
    public function getSiftingRandomFactor()
    {
        return $this->siftingRandomFactor;
    }

    /**
     * Set the sifting value
     *
     * @param unknown $siftingValue new value being set
     *
     * @return EcmtPermits
     */
    public function setSiftingValue($siftingValue)
    {
        $this->siftingValue = $siftingValue;

        return $this;
    }

    /**
     * Get the sifting value
     *
     * @return unknown
     */
    public function getSiftingValue()
    {
        return $this->siftingValue;
    }

    /**
     * Set the sifting value random
     *
     * @param unknown $siftingValueRandom new value being set
     *
     * @return EcmtPermits
     */
    public function setSiftingValueRandom($siftingValueRandom)
    {
        $this->siftingValueRandom = $siftingValueRandom;

        return $this;
    }

    /**
     * Get the sifting value random
     *
     * @return unknown
     */
    public function getSiftingValueRandom()
    {
        return $this->siftingValueRandom;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return EcmtPermits
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
     * @param int $version new value being set
     *
     * @return EcmtPermits
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
                if ($this->$property instanceof Collection) {
                    $this->$property = new ArrayCollection(array());
                } else {
                    $this->$property = null;
                }
            }
        }
    }
}
