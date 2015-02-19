<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FinancialStandingRate Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="financial_standing_rate",
 *    indexes={
 *        @ORM\Index(name="fk_financial_standing_rate_ref_data1_idx", columns={"licence_type"}),
 *        @ORM\Index(name="fk_financial_standing_rate_ref_data2_idx", columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_financial_standing_rate_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_financial_standing_rate_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class FinancialStandingRate implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\GoodsOrPsvManyToOneAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceTypeManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Additional vehicle rate
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="additional_vehicle_rate", precision=10, scale=2, nullable=true)
     */
    protected $additionalVehicleRate;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_from", nullable=false)
     */
    protected $effectiveFrom;

    /**
     * First vehicle rate
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="first_vehicle_rate", precision=10, scale=2, nullable=true)
     */
    protected $firstVehicleRate;

    /**
     * Set the additional vehicle rate
     *
     * @param float $additionalVehicleRate
     * @return FinancialStandingRate
     */
    public function setAdditionalVehicleRate($additionalVehicleRate)
    {
        $this->additionalVehicleRate = $additionalVehicleRate;

        return $this;
    }

    /**
     * Get the additional vehicle rate
     *
     * @return float
     */
    public function getAdditionalVehicleRate()
    {
        return $this->additionalVehicleRate;
    }

    /**
     * Set the effective from
     *
     * @param \DateTime $effectiveFrom
     * @return FinancialStandingRate
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
     * Set the first vehicle rate
     *
     * @param float $firstVehicleRate
     * @return FinancialStandingRate
     */
    public function setFirstVehicleRate($firstVehicleRate)
    {
        $this->firstVehicleRate = $firstVehicleRate;

        return $this;
    }

    /**
     * Get the first vehicle rate
     *
     * @return float
     */
    public function getFirstVehicleRate()
    {
        return $this->firstVehicleRate;
    }
}
