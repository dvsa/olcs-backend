<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpPermitType Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_type",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_type_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_type_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitType extends AbstractIrhpPermitType
{

}
