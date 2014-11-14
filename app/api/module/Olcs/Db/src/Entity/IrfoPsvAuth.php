<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoPsvAuth Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_psv_auth",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_psv_auth_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data1_idx", 
 *            columns={"journey_frequency"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_irfo_psv_auth_type1_idx", 
 *            columns={"irfo_psv_auth_type_id"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data2_idx", 
 *            columns={"status"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data3_idx", 
 *            columns={"withdrawn_reason"})
 *    }
 * )
 */
class IrfoPsvAuth implements Interfaces\EntityInterface
{

    /**
     * Journey frequency
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="journey_frequency", referencedColumnName="id", nullable=true)
     */
    protected $journeyFrequency;

    /**
     * Irfo psv auth type
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuthType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuthType", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoPsvAuthType;

    /**
     * Is fee exempt application
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt_application", nullable=false)
     */
    protected $isFeeExemptApplication = 0;

    /**
     * Is fee exempt annual
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt_annual", nullable=false)
     */
    protected $isFeeExemptAnnual = 0;

    /**
     * Irfo fee id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="irfo_fee_id", length=10, nullable=false)
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
     * Copies issued
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_issued", nullable=false)
     */
    protected $copiesIssued = 0;

    /**
     * Copies required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_required", nullable=false)
     */
    protected $copiesRequired = 0;

    /**
     * Copies required total
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_required_total", nullable=false)
     */
    protected $copiesRequiredTotal = 0;

    /**
     * Copies issued total
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_issued_total", nullable=false)
     */
    protected $copiesIssuedTotal = 0;

    /**
     * Last date copies req
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_date_copies_req", nullable=true)
     */
    protected $lastDateCopiesReq;

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
     * Validity period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="validity_period", nullable=false)
     */
    protected $validityPeriod;

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
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Withdrawn reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="withdrawn_reason", referencedColumnName="id", nullable=true)
     */
    protected $withdrawnReason;

    /**
     * Status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

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
     * In force date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="in_force_date", nullable=true)
     */
    protected $inForceDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the journey frequency
     *
     * @param \Olcs\Db\Entity\RefData $journeyFrequency
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getJourneyFrequency()
    {
        return $this->journeyFrequency;
    }

    /**
     * Set the irfo psv auth type
     *
     * @param \Olcs\Db\Entity\IrfoPsvAuthType $irfoPsvAuthType
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
     * @return \Olcs\Db\Entity\IrfoPsvAuthType
     */
    public function getIrfoPsvAuthType()
    {
        return $this->irfoPsvAuthType;
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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the withdrawn reason
     *
     * @param \Olcs\Db\Entity\RefData $withdrawnReason
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setWithdrawnReason($withdrawnReason)
    {
        $this->withdrawnReason = $withdrawnReason;

        return $this;
    }

    /**
     * Get the withdrawn reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getWithdrawnReason()
    {
        return $this->withdrawnReason;
    }

    /**
     * Set the status
     *
     * @param \Olcs\Db\Entity\RefData $status
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the exemption details
     *
     * @param string $exemptionDetails
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the in force date
     *
     * @param \DateTime $inForceDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
