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
        $instance->irhpPermitType = $type;
        $instance->validFrom = static::processDate($validFrom);
        $instance->validTo = static::processDate($validTo);
        $instance->initialStock = $quota;

        return $instance;
    }
}
