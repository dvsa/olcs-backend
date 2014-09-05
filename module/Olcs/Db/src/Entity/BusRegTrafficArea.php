<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusRegTrafficArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_reg_traffic_area",
 *    indexes={
 *        @ORM\Index(name="fk_bus_reg_traffic_area_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_bus_reg_traffic_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_reg_traffic_area_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_B7B2BC1018E0B1DB", columns={"traffic_area_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="bus_reg_ta_unique", columns={"traffic_area_id","bus_reg_id"})
 *    }
 * )
 */
class BusRegTrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\BusRegManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Txc missing
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="txc_missing", nullable=true)
     */
    protected $txcMissing;

    /**
     * Txc not required
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="txc_not_required", nullable=true)
     */
    protected $txcNotRequired;

    /**
     * Set the txc missing
     *
     * @param string $txcMissing
     * @return BusRegTrafficArea
     */
    public function setTxcMissing($txcMissing)
    {
        $this->txcMissing = $txcMissing;

        return $this;
    }

    /**
     * Get the txc missing
     *
     * @return string
     */
    public function getTxcMissing()
    {
        return $this->txcMissing;
    }


    /**
     * Set the txc not required
     *
     * @param string $txcNotRequired
     * @return BusRegTrafficArea
     */
    public function setTxcNotRequired($txcNotRequired)
    {
        $this->txcNotRequired = $txcNotRequired;

        return $this;
    }

    /**
     * Get the txc not required
     *
     * @return string
     */
    public function getTxcNotRequired()
    {
        return $this->txcNotRequired;
    }

}
