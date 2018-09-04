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
     *     columns={"irhp_permit_type_id"}),
 *        @ORM\Index(name="fk_irhp_permit_stock_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_stock_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitStock extends AbstractIrhpPermitStock
{

}
