<?php

namespace Dvsa\Olcs\Api\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusNoticePeriod Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="bus_notice_period",
 *    indexes={
 *        @ORM\Index(name="ix_bus_notice_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_bus_notice_period_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class BusNoticePeriod extends AbstractBusNoticePeriod
{
    const NOTICE_PERIOD_SCOTLAND = 1;
    const NOTICE_PERIOD_OTHER = 2;
    const NOTICE_PERIOD_WALES = 3;

    /**
     * Returns whether the notice period is scottish rules, usually called from the parent busReg
     *
     * @return bool
     */
    public function isScottishRules()
    {
        return $this->id === self::NOTICE_PERIOD_SCOTLAND;
    }

    public function isWalesRules()
    {
        return $this->id = self::NOTICE_PERIOD_WALES;
    }
}
