<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

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
    public static function create($type, $validFrom, $validTo, $quota)
    {
        $instance = new self;

        $instance->irhpPermitType = $type;
        $instance->validFrom = static::processDate($validFrom, 'Y-m-d');
        $instance->validTo = static::processDate($validTo, 'Y-m-d');
        $instance->initialStock = intval($quota) > 0 ? $quota : 0;

        return $instance;
    }

    public function update($type, $validFrom, $validTo, $quota)
    {
        $this->irhpPermitType = $type;
        $this->validFrom = static::processDate($validFrom, 'Y-m-d');
        $this->validTo = static::processDate($validTo, 'Y-m-d');
        $this->initialStock = intval($quota) > 0 ? $quota : 0;

        return $this;
    }
}
