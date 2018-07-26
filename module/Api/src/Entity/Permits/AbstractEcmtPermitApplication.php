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
 * EcmtPermitApplication Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_permit_type", columns={"permit_type"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_payment_status", columns={"payment_status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_sectors_id", columns={"sectors_id"})
 *    }
 * )
 */
abstract class AbstractEcmtPermitApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Cabotage
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="cabotage", nullable=true)
     */
    protected $cabotage;

    /**
     * Country
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country",
     *     inversedBy="ecmtApplications",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="ecmt_application_country_link",
     *     joinColumns={
     *         @ORM\JoinColumn(name="ecmt_application_id", referencedColumnName="id")
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
     * Declaration
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="declaration", nullable=true)
     */
    protected $declaration;

    /**
     * Emissions
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="emissions", nullable=true)
     */
    protected $emissions;

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
     * International journeys
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="international_journeys", nullable=true)
     */
    protected $internationalJourneys;

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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * No of permits
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_permits", nullable=true)
     */
    protected $noOfPermits;

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
     * Permit type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="permit_type", referencedColumnName="id", nullable=false)
     */
    protected $permitType;

    /**
     * Permits required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="permits_required", nullable=true)
     */
    protected $permitsRequired;

    /**
     * Sectors
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\Sectors
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\Sectors", fetch="LAZY")
     * @ORM\JoinColumn(name="sectors_id", referencedColumnName="id", nullable=true)
     */
    protected $sectors;

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
     * Trips
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="trips", nullable=true)
     */
    protected $trips;

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
    }

    /**
     * Set the cabotage
     *
     * @param boolean $cabotage new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setCabotage($cabotage)
    {
        $this->cabotage = $cabotage;

        return $this;
    }

    /**
     * Get the cabotage
     *
     * @return boolean
     */
    public function getCabotage()
    {
        return $this->cabotage;
    }

    /**
     * Set the country
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys collection being set as the value
     *
     * @return EcmtPermitApplication
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
     * @return EcmtPermitApplication
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
     * @return EcmtPermitApplication
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
     * @return EcmtPermitApplication
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
     * @return EcmtPermitApplication
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
     * Set the declaration
     *
     * @param boolean $declaration new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setDeclaration($declaration)
    {
        $this->declaration = $declaration;

        return $this;
    }

    /**
     * Get the declaration
     *
     * @return boolean
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    /**
     * Set the emissions
     *
     * @param boolean $emissions new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setEmissions($emissions)
    {
        $this->emissions = $emissions;

        return $this;
    }

    /**
     * Get the emissions
     *
     * @return boolean
     */
    public function getEmissions()
    {
        return $this->emissions;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return EcmtPermitApplication
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
     * Set the international journeys
     *
     * @param int $internationalJourneys new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setInternationalJourneys($internationalJourneys)
    {
        $this->internationalJourneys = $internationalJourneys;

        return $this;
    }

    /**
     * Get the international journeys
     *
     * @return int
     */
    public function getInternationalJourneys()
    {
        return $this->internationalJourneys;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return EcmtPermitApplication
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
     * @return EcmtPermitApplication
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence entity being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the no of permits
     *
     * @param int $noOfPermits new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setNoOfPermits($noOfPermits)
    {
        $this->noOfPermits = $noOfPermits;

        return $this;
    }

    /**
     * Get the no of permits
     *
     * @return int
     */
    public function getNoOfPermits()
    {
        return $this->noOfPermits;
    }

    /**
     * Set the payment status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $paymentStatus entity being set as the value
     *
     * @return EcmtPermitApplication
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
     * Set the permit type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $permitType entity being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setPermitType($permitType)
    {
        $this->permitType = $permitType;

        return $this;
    }

    /**
     * Get the permit type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getPermitType()
    {
        return $this->permitType;
    }

    /**
     * Set the permits required
     *
     * @param int $permitsRequired new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setPermitsRequired($permitsRequired)
    {
        $this->permitsRequired = $permitsRequired;

        return $this;
    }

    /**
     * Get the permits required
     *
     * @return int
     */
    public function getPermitsRequired()
    {
        return $this->permitsRequired;
    }

    /**
     * Set the sectors
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\Sectors $sectors entity being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setSectors($sectors)
    {
        $this->sectors = $sectors;

        return $this;
    }

    /**
     * Get the sectors
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\Sectors
     */
    public function getSectors()
    {
        return $this->sectors;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return EcmtPermitApplication
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
     * Set the trips
     *
     * @param int $trips new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setTrips($trips)
    {
        $this->trips = $trips;

        return $this;
    }

    /**
     * Get the trips
     *
     * @return int
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return EcmtPermitApplication
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
