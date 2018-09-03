<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitRange Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_range",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_ranges_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"})
 *    }
 * )
 */
class IrhpPermitRange extends AbstractIrhpPermitRange
{

}
