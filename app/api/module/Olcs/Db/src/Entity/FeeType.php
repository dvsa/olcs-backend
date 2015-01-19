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
 *        @ORM\Index(name="fk_fee_type_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_fee_type_ref_data1_idx", columns={"licence_type"}),
 *        @ORM\Index(name="fk_fee_type_ref_data2_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_fee_type_ref_data3_idx", columns={"accrual_rule"}),
 *        @ORM\Index(name="fk_fee_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_fee_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class FeeType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description255Field,
        Traits\GoodsOrPsvManyToOneAlt1,
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
     * @ORM\Column(type="yesno", name="expire_fee_with_licence", nullable=false)
     */
    protected $expireFeeWithLicence = 0;

    /**
     * Fee type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fee_type", length=20, nullable=false)
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
     * @param string $feeType
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
     * @return string
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
}
