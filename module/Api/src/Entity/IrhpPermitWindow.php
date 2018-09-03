<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitWindow Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_window",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_windows_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"})
 *    }
 * )
 */
class IrhpPermitWindow extends AbstractIrhpPermitWindow
{

}
