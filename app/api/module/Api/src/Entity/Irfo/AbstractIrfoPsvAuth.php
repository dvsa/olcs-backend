<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * IrfoPsvAuth Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_psv_auth",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_psv_auth_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_journey_frequency", columns={"journey_frequency"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_irfo_psv_auth_type_id", columns={"irfo_psv_auth_type_id"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_status", columns={"status"}),
 *        @ORM\Index(name="ix_irfo_psv_auth_withdrawn_reason", columns={"withdrawn_reason"})
 *    }
 * )
 */
abstract class AbstractIrfoPsvAuth implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Application sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="application_sent_date", nullable=true)
     */
    protected $applicationSentDate;

    /**
     * Copies issued
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="copies_issued", nullable=false, options={"default": 0})
     */
    protected $copiesIssued = 0;

    /**
     * Copies issued total
     *
     * @var int
     *
     * @ORM\Column(type="smallint",
     *     name="copies_issued_total",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $copiesIssuedTotal = 0;

    /**
     * Copies required
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="copies_required", nullable=false, options={"default": 0})
     */
    protected $copiesRequired = 0;

    /**
     * Copies required total
     *
     * @var int
     *
     * @ORM\Column(type="smallint",
     *     name="copies_required_total",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $copiesRequiredTotal = 0;

    /**
     * Country
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Country",
     *     inversedBy="irfoPsvAuths",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="irfo_psv_auth_country",
     *     joinColumns={
     *         @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id")
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
     * Exemption details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="exemption_details", length=255, nullable=true)
     */
    protected $exemptionDetails;

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
     * Irfo fee id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_fee_id", length=10, nullable=true)
     */
    protected $irfoFeeId;

    /**
     * Irfo file no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_file_no", length=10, nullable=false)
     */
    protected $irfoFileNo;

    /**
     * Irfo psv auth type
     *
     * @var \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoPsvAuthType;

    /**
     * Is fee exempt annual
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="is_fee_exempt_annual",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $isFeeExemptAnnual = 0;

    /**
     * Is fee exempt application
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="is_fee_exempt_application",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $isFeeExemptApplication = 0;

    /**
     * Journey frequency
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="journey_frequency", referencedColumnName="id", nullable=true)
     */
    protected $journeyFrequency;

    /**
     * Last date copies req
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_date_copies_req", nullable=true)
     */
    protected $lastDateCopiesReq;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
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
     * Organisation
     *
     * @var \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Organisation\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Renewal date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="renewal_date", nullable=true)
     */
    protected $renewalDate;

    /**
     * Service route from
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_route_from", length=30, nullable=false)
     */
    protected $serviceRouteFrom;

    /**
     * Service route to
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_route_to", length=30, nullable=false)
     */
    protected $serviceRouteTo;

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
     * Validity period
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="validity_period", nullable=false)
     */
    protected $validityPeriod;

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
     * Withdrawn reason
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawnReason;

    /**
     * Irfo psv auth number
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber",
     *     mappedBy="irfoPsvAuth",
     *     cascade={"persist"}
     * )
     */
    protected $irfoPsvAuthNumbers;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->countrys = new ArrayCollection();
        $this->irfoPsvAuthNumbers = new ArrayCollection();
    }

    /**
     * Set the application sent date
     *
     * @param \DateTime $applicationSentDate
     * @return IrfoPsvAuth
     */
    public function setApplicationSentDate($applicationSentDate)
    {
        $this->applicationSentDate = $applicationSentDate;

        return $this;
    }

    /**
     * Get the application sent date
     *
     * @return \DateTime
     */
    public function getApplicationSentDate()
    {
        return $this->applicationSentDate;
    }

    /**
     * Set the copies issued
     *
     * @param int $copiesIssued
     * @return IrfoPsvAuth
     */
    public function setCopiesIssued($copiesIssued)
    {
        $this->copiesIssued = $copiesIssued;

        return $this;
    }

    /**
     * Get the copies issued
     *
     * @return int
     */
    public function getCopiesIssued()
    {
        return $this->copiesIssued;
    }

    /**
     * Set the copies issued total
     *
     * @param int $copiesIssuedTotal
     * @return IrfoPsvAuth
     */
    public function setCopiesIssuedTotal($copiesIssuedTotal)
    {
        $this->copiesIssuedTotal = $copiesIssuedTotal;

        return $this;
    }

    /**
     * Get the copies issued total
     *
     * @return int
     */
    public function getCopiesIssuedTotal()
    {
        return $this->copiesIssuedTotal;
    }

    /**
     * Set the copies required
     *
     * @param int $copiesRequired
     * @return IrfoPsvAuth
     */
    public function setCopiesRequired($copiesRequired)
    {
        $this->copiesRequired = $copiesRequired;

        return $this;
    }

    /**
     * Get the copies required
     *
     * @return int
     */
    public function getCopiesRequired()
    {
        return $this->copiesRequired;
    }

    /**
     * Set the copies required total
     *
     * @param int $copiesRequiredTotal
     * @return IrfoPsvAuth
     */
    public function setCopiesRequiredTotal($copiesRequiredTotal)
    {
        $this->copiesRequiredTotal = $copiesRequiredTotal;

        return $this;
    }

    /**
     * Get the copies required total
     *
     * @return int
     */
    public function getCopiesRequiredTotal()
    {
        return $this->copiesRequiredTotal;
    }

    /**
     * Set the country
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys
     * @return IrfoPsvAuth
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
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys
     * @return IrfoPsvAuth
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
     * @param \Doctrine\Common\Collections\ArrayCollection $countrys
     * @return IrfoPsvAuth
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return IrfoPsvAuth
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
     * @return IrfoPsvAuth
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
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return IrfoPsvAuth
     */
    public function setExemptionDetails($exemptionDetails)
    {
        $this->exemptionDetails = $exemptionDetails;

        return $this;
    }

    /**
     * Get the exemption details
     *
     * @return string
     */
    public function getExemptionDetails()
    {
        return $this->exemptionDetails;
    }

    /**
     * Set the expiry date
     *
     * @param \DateTime $expiryDate
     * @return IrfoPsvAuth
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get the expiry date
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return IrfoPsvAuth
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
     * @param \DateTime $inForceDate
     * @return IrfoPsvAuth
     */
    public function setInForceDate($inForceDate)
    {
        $this->inForceDate = $inForceDate;

        return $this;
    }

    /**
     * Get the in force date
     *
     * @return \DateTime
     */
    public function getInForceDate()
    {
        return $this->inForceDate;
    }

    /**
     * Set the irfo fee id
     *
     * @param string $irfoFeeId
     * @return IrfoPsvAuth
     */
    public function setIrfoFeeId($irfoFeeId)
    {
        $this->irfoFeeId = $irfoFeeId;

        return $this;
    }

    /**
     * Get the irfo fee id
     *
     * @return string
     */
    public function getIrfoFeeId()
    {
        return $this->irfoFeeId;
    }

    /**
     * Set the irfo file no
     *
     * @param string $irfoFileNo
     * @return IrfoPsvAuth
     */
    public function setIrfoFileNo($irfoFileNo)
    {
        $this->irfoFileNo = $irfoFileNo;

        return $this;
    }

    /**
     * Get the irfo file no
     *
     * @return string
     */
    public function getIrfoFileNo()
    {
        return $this->irfoFileNo;
    }

    /**
     * Set the irfo psv auth type
     *
     * @param \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType $irfoPsvAuthType
     * @return IrfoPsvAuth
     */
    public function setIrfoPsvAuthType($irfoPsvAuthType)
    {
        $this->irfoPsvAuthType = $irfoPsvAuthType;

        return $this;
    }

    /**
     * Get the irfo psv auth type
     *
     * @return \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType
     */
    public function getIrfoPsvAuthType()
    {
        return $this->irfoPsvAuthType;
    }

    /**
     * Set the is fee exempt annual
     *
     * @param string $isFeeExemptAnnual
     * @return IrfoPsvAuth
     */
    public function setIsFeeExemptAnnual($isFeeExemptAnnual)
    {
        $this->isFeeExemptAnnual = $isFeeExemptAnnual;

        return $this;
    }

    /**
     * Get the is fee exempt annual
     *
     * @return string
     */
    public function getIsFeeExemptAnnual()
    {
        return $this->isFeeExemptAnnual;
    }

    /**
     * Set the is fee exempt application
     *
     * @param string $isFeeExemptApplication
     * @return IrfoPsvAuth
     */
    public function setIsFeeExemptApplication($isFeeExemptApplication)
    {
        $this->isFeeExemptApplication = $isFeeExemptApplication;

        return $this;
    }

    /**
     * Get the is fee exempt application
     *
     * @return string
     */
    public function getIsFeeExemptApplication()
    {
        return $this->isFeeExemptApplication;
    }

    /**
     * Set the journey frequency
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $journeyFrequency
     * @return IrfoPsvAuth
     */
    public function setJourneyFrequency($journeyFrequency)
    {
        $this->journeyFrequency = $journeyFrequency;

        return $this;
    }

    /**
     * Get the journey frequency
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getJourneyFrequency()
    {
        return $this->journeyFrequency;
    }

    /**
     * Set the last date copies req
     *
     * @param \DateTime $lastDateCopiesReq
     * @return IrfoPsvAuth
     */
    public function setLastDateCopiesReq($lastDateCopiesReq)
    {
        $this->lastDateCopiesReq = $lastDateCopiesReq;

        return $this;
    }

    /**
     * Get the last date copies req
     *
     * @return \DateTime
     */
    public function getLastDateCopiesReq()
    {
        return $this->lastDateCopiesReq;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return IrfoPsvAuth
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
     * @return IrfoPsvAuth
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
     * Set the organisation
     *
     * @param \Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     * @return IrfoPsvAuth
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the renewal date
     *
     * @param \DateTime $renewalDate
     * @return IrfoPsvAuth
     */
    public function setRenewalDate($renewalDate)
    {
        $this->renewalDate = $renewalDate;

        return $this;
    }

    /**
     * Get the renewal date
     *
     * @return \DateTime
     */
    public function getRenewalDate()
    {
        return $this->renewalDate;
    }

    /**
     * Set the service route from
     *
     * @param string $serviceRouteFrom
     * @return IrfoPsvAuth
     */
    public function setServiceRouteFrom($serviceRouteFrom)
    {
        $this->serviceRouteFrom = $serviceRouteFrom;

        return $this;
    }

    /**
     * Get the service route from
     *
     * @return string
     */
    public function getServiceRouteFrom()
    {
        return $this->serviceRouteFrom;
    }

    /**
     * Set the service route to
     *
     * @param string $serviceRouteTo
     * @return IrfoPsvAuth
     */
    public function setServiceRouteTo($serviceRouteTo)
    {
        $this->serviceRouteTo = $serviceRouteTo;

        return $this;
    }

    /**
     * Get the service route to
     *
     * @return string
     */
    public function getServiceRouteTo()
    {
        return $this->serviceRouteTo;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status
     * @return IrfoPsvAuth
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
     * Set the validity period
     *
     * @param int $validityPeriod
     * @return IrfoPsvAuth
     */
    public function setValidityPeriod($validityPeriod)
    {
        $this->validityPeriod = $validityPeriod;

        return $this;
    }

    /**
     * Get the validity period
     *
     * @return int
     */
    public function getValidityPeriod()
    {
        return $this->validityPeriod;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return IrfoPsvAuth
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
     * Set the withdrawn reason
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $withdrawnReason
     * @return IrfoPsvAuth
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }

    /**
     * Set the irfo psv auth number
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuthNumbers
     * @return IrfoPsvAuth
     */
    public function setIrfoPsvAuthNumbers($irfoPsvAuthNumbers)
    {
        $this->irfoPsvAuthNumbers = $irfoPsvAuthNumbers;

        return $this;
    }

    /**
     * Get the irfo psv auth numbers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrfoPsvAuthNumbers()
    {
        return $this->irfoPsvAuthNumbers;
    }

    /**
     * Add a irfo psv auth numbers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuthNumbers
     * @return IrfoPsvAuth
     */
    public function addIrfoPsvAuthNumbers($irfoPsvAuthNumbers)
    {
        if ($irfoPsvAuthNumbers instanceof ArrayCollection) {
            $this->irfoPsvAuthNumbers = new ArrayCollection(
                array_merge(
                    $this->irfoPsvAuthNumbers->toArray(),
                    $irfoPsvAuthNumbers->toArray()
                )
            );
        } elseif (!$this->irfoPsvAuthNumbers->contains($irfoPsvAuthNumbers)) {
            $this->irfoPsvAuthNumbers->add($irfoPsvAuthNumbers);
        }

        return $this;
    }

    /**
     * Remove a irfo psv auth numbers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuthNumbers
     * @return IrfoPsvAuth
     */
    public function removeIrfoPsvAuthNumbers($irfoPsvAuthNumbers)
    {
        if ($this->irfoPsvAuthNumbers->contains($irfoPsvAuthNumbers)) {
            $this->irfoPsvAuthNumbers->removeElement($irfoPsvAuthNumbers);
        }

        return $this;
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
