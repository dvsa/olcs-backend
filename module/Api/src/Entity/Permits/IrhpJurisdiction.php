<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrhpJurisdiction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_jurisdiction",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_jurisdiction_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_jurisdiction_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpJurisdiction extends AbstractIrhpJurisdiction
{

}
