<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
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
 *        @ORM\Index(name="ecmt_permits_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_permits_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ecmt_ecmt_permits_application_id", columns={"ecmt_permits_application_id"})
 *    }
 * )
 */
abstract class AbstractEcmtPermits implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Application status id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="application_status_id", nullable=false)
     */
    protected $applicationStatusId;

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
     * Ecmt countries ids
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ecmt_countries_ids", length=32, nullable=false)
     */
    protected $ecmtCountriesIds;

    /**
     * Ecmt permits application
     *
     * @var \Dvsa\Olcs\Api\Entity\EcmtPermitsApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\EcmtPermitsApplication", fetch="LAZY")
     * @ORM\JoinColumn(name="ecmt_permits_application_id",
     *     referencedColumnName="application_id",
     *     nullable=false)
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
     * Payment status id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="payment_status_id", nullable=false)
     */
    protected $paymentStatusId;

    /**
     * Sector id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="sector_id", nullable=false)
     */
    protected $sectorId;

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
     * @return EcmtPermits
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
     * Set the ecmt countries ids
     *
     * @param string $ecmtCountriesIds new value being set
     *
     * @return EcmtPermits
     */
    public function setEcmtCountriesIds($ecmtCountriesIds)
    {
        $this->ecmtCountriesIds = $ecmtCountriesIds;

        return $this;
    }

    /**
     * Get the ecmt countries ids
     *
     * @return string
     */
    public function getEcmtCountriesIds()
    {
        return $this->ecmtCountriesIds;
    }

    /**
     * Set the ecmt permits application
     *
     * @param \Dvsa\Olcs\Api\Entity\EcmtPermitsApplication $ecmtPermitsApplication entity being set as the value
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
     * @return \Dvsa\Olcs\Api\Entity\EcmtPermitsApplication
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
     * Set the payment status id
     *
     * @param int $paymentStatusId new value being set
     *
     * @return EcmtPermits
     */
    public function setPaymentStatusId($paymentStatusId)
    {
        $this->paymentStatusId = $paymentStatusId;

        return $this;
    }

    /**
     * Get the payment status id
     *
     * @return int
     */
    public function getPaymentStatusId()
    {
        return $this->paymentStatusId;
    }

    /**
     * Set the sector id
     *
     * @param int $sectorId new value being set
     *
     * @return EcmtPermits
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
                $this->$property = null;
            }
        }
    }
}
