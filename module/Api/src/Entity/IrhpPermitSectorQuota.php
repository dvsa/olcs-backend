<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitSectorQuota Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_sector_quota",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_quotas_irhp_sectors1_idx", columns={"sector_id"}),
 *        @ORM\Index(name="fk_irhp_permit_quotas_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"})
 *    }
 * )
 */
class IrhpPermitSectorQuota extends AbstractIrhpPermitSectorQuota
{

}
