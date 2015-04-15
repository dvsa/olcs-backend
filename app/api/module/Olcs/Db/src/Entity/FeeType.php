<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * FeeType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee_type",
 *    indexes={
 *        @ORM\Index(name="ix_fee_type_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_fee_type_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_fee_type_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_fee_type_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_type_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_type_accrual_rule", columns={"accrual_rule"}),
 *        @ORM\Index(name="ix_fee_type_fee_type", columns={"fee_type"}),
 *        @ORM\Index(name="ix_fee_type_is_miscellaneous", columns={"is_miscellaneous"})
 *    }
 * )
 */
class FeeType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\GoodsOrPsvManyToOne,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceTypeManyToOne,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Accrual rule
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="accrual_rule", referencedColumnName="id", nullable=false)
     */
    protected $accrualRule;

    /**
     * Annual value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="annual_value", precision=10, scale=2, nullable=true)
     */
    protected $annualValue;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="effective_from", nullable=false)
     */
    protected $effectiveFrom;

    /**
     * Expire fee with licence
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="expire_fee_with_licence", nullable=false, options={"default": 0})
     */
    protected $expireFeeWithLicence = 0;

    /**
     * Fee type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="fee_type", referencedColumnName="id", nullable=false)
     */
    protected $feeType;

    /**
     * Five year value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="five_year_value", precision=10, scale=2, nullable=true)
     */
    protected $fiveYearValue;

    /**
     * Fixed value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="fixed_value", precision=10, scale=2, nullable=true)
     */
    protected $fixedValue;

    /**
     * Is miscellaneous
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_miscellaneous", nullable=false, options={"default": 0})
     */
    protected $isMiscellaneous = 0;

    /**
     * Set the accrual rule
     *
     * @param \Olcs\Db\Entity\RefData $accrualRule
     * @return FeeType
     */
    public function setAccrualRule($accrualRule)
    {
        $this->accrualRule = $accrualRule;

        return $this;
    }

    /**
     * Get the accrual rule
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getAccrualRule()
    {
        return $this->accrualRule;
    }

    /**
     * Set the annual value
     *
     * @param float $annualValue
     * @return FeeType
     */
    public function setAnnualValue($annualValue)
    {
        $this->annualValue = $annualValue;

        return $this;
    }

    /**
     * Get the annual value
     *
     * @return float
     */
    public function getAnnualValue()
    {
        return $this->annualValue;
    }

    /**
     * Set the effective from
     *
     * @param \DateTime $effectiveFrom
     * @return FeeType
     */
    public function setEffectiveFrom($effectiveFrom)
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }

    /**
     * Get the effective from
     *
     * @return \DateTime
     */
    public function getEffectiveFrom()
    {
        return $this->effectiveFrom;
    }

    /**
     * Set the expire fee with licence
     *
     * @param string $expireFeeWithLicence
     * @return FeeType
     */
    public function setExpireFeeWithLicence($expireFeeWithLicence)
    {
        $this->expireFeeWithLicence = $expireFeeWithLicence;

        return $this;
    }

    /**
     * Get the expire fee with licence
     *
     * @return string
     */
    public function getExpireFeeWithLicence()
    {
        return $this->expireFeeWithLicence;
    }

    /**
     * Set the fee type
     *
     * @param \Olcs\Db\Entity\RefData $feeType
     * @return FeeType
     */
    public function setFeeType($feeType)
    {
        $this->feeType = $feeType;

        return $this;
    }

    /**
     * Get the fee type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * Set the five year value
     *
     * @param float $fiveYearValue
     * @return FeeType
     */
    public function setFiveYearValue($fiveYearValue)
    {
        $this->fiveYearValue = $fiveYearValue;

        return $this;
    }

    /**
     * Get the five year value
     *
     * @return float
     */
    public function getFiveYearValue()
    {
        return $this->fiveYearValue;
    }

    /**
     * Set the fixed value
     *
     * @param float $fixedValue
     * @return FeeType
     */
    public function setFixedValue($fixedValue)
    {
        $this->fixedValue = $fixedValue;

        return $this;
    }

    /**
     * Get the fixed value
     *
     * @return float
     */
    public function getFixedValue()
    {
        return $this->fixedValue;
    }

    /**
     * Set the is miscellaneous
     *
     * @param boolean $isMiscellaneous
     * @return FeeType
     */
    public function setIsMiscellaneous($isMiscellaneous)
    {
        $this->isMiscellaneous = $isMiscellaneous;

        return $this;
    }

    /**
     * Get the is miscellaneous
     *
     * @return boolean
     */
    public function getIsMiscellaneous()
    {
        return $this->isMiscellaneous;
    }
}
