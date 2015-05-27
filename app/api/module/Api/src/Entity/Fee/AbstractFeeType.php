<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * FeeType Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
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
abstract class AbstractFeeType implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Accrual rule
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=false)
     */
    protected $description;

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
     * @ORM\Column(type="yesno",
     *     name="expire_fee_with_licence",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $expireFeeWithLicence = 0;

    /**
     * Fee type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
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
     * Goods or psv
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="goods_or_psv", referencedColumnName="id", nullable=true)
     */
    protected $goodsOrPsv;

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
     * Is miscellaneous
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_miscellaneous", nullable=false, options={"default": 0})
     */
    protected $isMiscellaneous = 0;

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
     * Licence type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_type", referencedColumnName="id", nullable=true)
     */
    protected $licenceType;

    /**
     * Traffic area
     *
     * @var \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

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
     * Set the accrual rule
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $accrualRule
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return FeeType
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
     * @return FeeType
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
     * Set the description
     *
     * @param string $description
     * @return FeeType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $feeType
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
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
     * Set the goods or psv
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $goodsOrPsv
     * @return FeeType
     */
    public function setGoodsOrPsv($goodsOrPsv)
    {
        $this->goodsOrPsv = $goodsOrPsv;

        return $this;
    }

    /**
     * Get the goods or psv
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return FeeType
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

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return FeeType
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
     * @return FeeType
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
     * Set the licence type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $licenceType
     * @return FeeType
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }

    /**
     * Set the traffic area
     *
     * @param \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $trafficArea
     * @return FeeType
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return FeeType
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
