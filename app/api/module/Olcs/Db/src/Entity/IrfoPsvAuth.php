<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoPsvAuth Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_psv_auth",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_psv_auth_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data1_idx", columns={"journey_frequency"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_irfo_psv_auth_type1_idx", columns={"irfo_psv_auth_type_id"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data2_idx", columns={"status"}),
 *        @ORM\Index(name="fk_irfo_psv_auth_ref_data3_idx", columns={"withdrawn_reason"})
 *    }
 * )
 */
class IrfoPsvAuth implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\ExemptionDetails255Field,
        Traits\ExpiryDateField,
        Traits\IdIdentity,
        Traits\InForceDateField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OrganisationManyToOne,
        Traits\StatusManyToOne,
        Traits\CustomVersionField,
        Traits\WithdrawnReasonManyToOne;

    /**
     * Copies issued
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_issued", nullable=false, options={"default": 0})
     */
    protected $copiesIssued = 0;

    /**
     * Copies issued total
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_issued_total", nullable=false, options={"default": 0})
     */
    protected $copiesIssuedTotal = 0;

    /**
     * Copies required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_required", nullable=false, options={"default": 0})
     */
    protected $copiesRequired = 0;

    /**
     * Copies required total
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="copies_required_total", nullable=false, options={"default": 0})
     */
    protected $copiesRequiredTotal = 0;

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
     * Irfo psv auth type
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuthType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuthType")
     * @ORM\JoinColumn(name="irfo_psv_auth_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoPsvAuthType;

    /**
     * Is fee exempt annual
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt_annual", nullable=false, options={"default": 0})
     */
    protected $isFeeExemptAnnual = 0;

    /**
     * Is fee exempt application
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt_application", nullable=false, options={"default": 0})
     */
    protected $isFeeExemptApplication = 0;

    /**
     * Journey frequency
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
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
}
