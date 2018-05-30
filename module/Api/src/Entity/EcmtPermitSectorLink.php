<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtPermitSectorLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_sector_link",
 *    indexes={
 *        @ORM\Index(name="ecmt_permit_sector_link_sector_id", columns={"sector_id"}),
 *        @ORM\Index(name="ecmt_permit_sector_link_permit_id", columns={"ecmt_permit_id"}),
 *        @ORM\Index(name="ecmt_permit_sector_link_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_permit_sector_link_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtPermitSectorLink extends AbstractEcmtPermitSectorLink
{

}
