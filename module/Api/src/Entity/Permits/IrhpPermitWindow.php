<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitWindow Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_window",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_windows_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"}),
 *        @ORM\Index(name="fk_irhp_permit_window_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_window_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitWindow extends AbstractIrhpPermitWindow
{

}
