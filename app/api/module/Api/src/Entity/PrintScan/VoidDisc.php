<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoidDisc Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="void_disc",
 *    indexes={
 *        @ORM\Index(name="ix_void_disc_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_void_disc_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_void_disc_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_void_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_void_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_void_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class VoidDisc extends AbstractVoidDisc
{

}
