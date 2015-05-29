<?php

namespace Dvsa\Olcs\Api\Entity\Vehicle;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsDisc Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="goods_disc",
 *    indexes={
 *        @ORM\Index(name="ix_goods_disc_licence_vehicle_id", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="ix_goods_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_goods_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_goods_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class GoodsDisc extends AbstractGoodsDisc
{

}
