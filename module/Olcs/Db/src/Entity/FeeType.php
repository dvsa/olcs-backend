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
 *        @ORM\Index(name="fk_fee_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_fee_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class FeeType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\GoodsOrPsvManyToOne,
        Traits\LicenceTypeManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Fee type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fee_type", length=20, nullable=false)
     */
    protected $feeType;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_from", nullable=false)
     */
    protected $effectiveFrom;

    /**
     * Fixed value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="fixed_value", precision=10, scale=2, nullable=true)
     */
    protected $fixedValue;

    /**
     * Annual value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="annual_value", precision=10, scale=2, nullable=true)
     */
    protected $annualValue;

    /**
     * Five year value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="five_year_value", precision=10, scale=2, nullable=true)
     */
    protected $fiveYearValue;

    /**
     * Expire fee with licence
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="expire_fee_with_licence", nullable=false)
     */
    protected $expireFeeWithLicence = 0;


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
}
