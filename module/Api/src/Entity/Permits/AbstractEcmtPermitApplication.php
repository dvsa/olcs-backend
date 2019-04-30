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
 *        @ORM\Index(name="ix_ecmt_permit_application_sectors_id", columns={"sectors_id"}),
 *        @ORM\Index(name="fk_ecmt_permit_application_international_jouneys",
     *     columns={"international_journeys"}),
 *        @ORM\Index(name="ix_withdraw_reason", columns={"withdraw_reason"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_source", columns={"source"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_in_scope", columns={"in_scope"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_cancellation_date",
     *     columns={"cancellation_date"})
 *    }
 * )
 */
abstract class AbstractEcmtPermitApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;

    /**
     * Cabotage
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="cabotage", nullable=true)
     */
    protected $cabotage;

    /**
     * Cancellation date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cancellation_date", nullable=true)
     */
    protected $cancellationDate;

    /**
     * Checked answers
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="checked_answers", nullable=true)
     */
    protected $checkedAnswers;

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
     * Date received
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_received", nullable=true)
     */
    protected $dateReceived;

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
     * Has restricted countries
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="has_restricted_countries", nullable=true)
     */
    protected $hasRestrictedCountries;

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
     * In scope
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="in_scope", nullable=true, options={"default": 0})
     */
    protected $inScope = 0;

    /**
     * International journeys
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="international_journeys", referencedColumnName="id", nullable=true)
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
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence",
     *     fetch="LAZY",
     *     inversedBy="ecmtApplications"
     * )
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
     * Source
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="source", referencedColumnName="id", nullable=false)
     */
    protected $source;

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
     * Withdraw reason
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdraw_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawReason;

    /**
     * Fee
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Fee\Fee", mappedBy="ecmtPermitApplication")
     */
    protected $fees;

    /**
     * Irhp permit application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication",
     *     mappedBy="ecmtPermitApplication"
     * )
     */
    protected $irhpPermitApplications;

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
        $this->fees = new ArrayCollection();
        $this->irhpPermitApplications = new ArrayCollection();
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
     * Set the cancellation date
     *
     * @param \DateTime $cancellationDate new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setCancellationDate($cancellationDate)
    {
        $this->cancellationDate = $cancellationDate;

        return $this;
    }

    /**
     * Get the cancellation date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCancellationDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->cancellationDate);
        }

        return $this->cancellationDate;
    }

    /**
     * Set the checked answers
     *
     * @param boolean $checkedAnswers new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setCheckedAnswers($checkedAnswers)
    {
        $this->checkedAnswers = $checkedAnswers;

        return $this;
    }

    /**
     * Get the checked answers
     *
     * @return boolean
     */
    public function getCheckedAnswers()
    {
        return $this->checkedAnswers;
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
     * Set the date received
     *
     * @param \DateTime $dateReceived new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setDateReceived($dateReceived)
    {
        $this->dateReceived = $dateReceived;

        return $this;
    }

    /**
     * Get the date received
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDateReceived($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->dateReceived);
        }

        return $this->dateReceived;
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
     * Set the has restricted countries
     *
     * @param boolean $hasRestrictedCountries new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setHasRestrictedCountries($hasRestrictedCountries)
    {
        $this->hasRestrictedCountries = $hasRestrictedCountries;

        return $this;
    }

    /**
     * Get the has restricted countries
     *
     * @return boolean
     */
    public function getHasRestrictedCountries()
    {
        return $this->hasRestrictedCountries;
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
     * Set the in scope
     *
     * @param boolean $inScope new value being set
     *
     * @return EcmtPermitApplication
     */
    public function setInScope($inScope)
    {
        $this->inScope = $inScope;

        return $this;
    }

    /**
     * Get the in scope
     *
     * @return boolean
     */
    public function getInScope()
    {
        return $this->inScope;
    }

    /**
     * Set the international journeys
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $internationalJourneys entity being set as the value
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
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
     * Set the source
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $source entity being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the source
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getSource()
    {
        return $this->source;
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
     * Set the withdraw reason
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $withdrawReason entity being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setWithdrawReason($withdrawReason)
    {
        $this->withdrawReason = $withdrawReason;

        return $this;
    }

    /**
     * Get the withdraw reason
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getWithdrawReason()
    {
        return $this->withdrawReason;
    }

    /**
     * Set the fee
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * Get the fees
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Add a fees
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being added
     *
     * @return EcmtPermitApplication
     */
    public function addFees($fees)
    {
        if ($fees instanceof ArrayCollection) {
            $this->fees = new ArrayCollection(
                array_merge(
                    $this->fees->toArray(),
                    $fees->toArray()
                )
            );
        } elseif (!$this->fees->contains($fees)) {
            $this->fees->add($fees);
        }

        return $this;
    }

    /**
     * Remove a fees
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fees collection being removed
     *
     * @return EcmtPermitApplication
     */
    public function removeFees($fees)
    {
        if ($this->fees->contains($fees)) {
            $this->fees->removeElement($fees);
        }

        return $this;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being set as the value
     *
     * @return EcmtPermitApplication
     */
    public function setIrhpPermitApplications($irhpPermitApplications)
    {
        $this->irhpPermitApplications = $irhpPermitApplications;

        return $this;
    }

    /**
     * Get the irhp permit applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitApplications()
    {
        return $this->irhpPermitApplications;
    }

    /**
     * Add a irhp permit applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being added
     *
     * @return EcmtPermitApplication
     */
    public function addIrhpPermitApplications($irhpPermitApplications)
    {
        if ($irhpPermitApplications instanceof ArrayCollection) {
            $this->irhpPermitApplications = new ArrayCollection(
                array_merge(
                    $this->irhpPermitApplications->toArray(),
                    $irhpPermitApplications->toArray()
                )
            );
        } elseif (!$this->irhpPermitApplications->contains($irhpPermitApplications)) {
            $this->irhpPermitApplications->add($irhpPermitApplications);
        }

        return $this;
    }

    /**
     * Remove a irhp permit applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitApplications collection being removed
     *
     * @return EcmtPermitApplication
     */
    public function removeIrhpPermitApplications($irhpPermitApplications)
    {
        if ($this->irhpPermitApplications->contains($irhpPermitApplications)) {
            $this->irhpPermitApplications->removeElement($irhpPermitApplications);
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
