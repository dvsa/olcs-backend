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
 *    }
 * )
 */
class BusRegTrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaOneToOne,
        Traits\BusRegOneToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Txc missing
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="txc_missing", nullable=true)
     */
    protected $txcMissing;

    /**
     * Txc not required
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="txc_not_required", nullable=true)
     */
    protected $txcNotRequired;

    /**
     * Set the txc missing
     *
     * @param boolean $txcMissing
     * @return \Olcs\Db\Entity\BusRegTrafficArea
     */
    public function setTxcMissing($txcMissing)
    {
        $this->txcMissing = $txcMissing;

        return $this;
    }

    /**
     * Get the txc missing
     *
     * @return boolean
     */
    public function getTxcMissing()
    {
        return $this->txcMissing;
    }

    /**
     * Set the txc not required
     *
     * @param boolean $txcNotRequired
     * @return \Olcs\Db\Entity\BusRegTrafficArea
     */
    public function setTxcNotRequired($txcNotRequired)
    {
        $this->txcNotRequired = $txcNotRequired;

        return $this;
    }

    /**
     * Get the txc not required
     *
     * @return boolean
     */
    public function getTxcNotRequired()
    {
        return $this->txcNotRequired;
    }
}
