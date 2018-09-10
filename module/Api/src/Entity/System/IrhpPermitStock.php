<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Logging\Log\Logger;

/**
 * IrhpPermitStock Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_stock",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_irhp_permit_types1_idx",
     *     columns={"irhp_permit_type_id"})
 *    }
 * )
 */
class IrhpPermitStock extends AbstractIrhpPermitStock
{
    public function create($type, $validFrom, $validTo, $quota) {
        $instance = new self;

        $formatFrom = $instance->formatDate($validFrom);
        $formatTo = $instance->formatDate($validTo);

        $instance->irhpPermitType = $type;
        $instance->validFrom = static::processDate($formatFrom, 'd-m-Y');
        $instance->validTo = static::processDate($formatTo, 'd-m-Y');
        $instance->initialStock = intval($quota) > 0 ? $quota : 0;

        return $instance;
    }

    public function update($type, $validFrom, $validTo, $quota) {
        $formatFrom = $this->formatDate($validFrom);
        $formatTo = $this->formatDate($validTo);

        $this->irhpPermitType = $type;
        $this->validFrom = static::processDate($formatFrom, 'd-m-Y');
        $this->validTo = static::processDate($formatTo, 'd-m-Y');
        $this->initialStock = intval($quota) > 0 ? $quota : 0;

        return $this;
    }

    private function formatDate($date) {
        return $date['day'] . '-' . $date['month'] . '-' . $date['year'];
    }
}
