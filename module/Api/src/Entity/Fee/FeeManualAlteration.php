<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeeManualAlteration Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="fee_manual_alteration",
 *    indexes={
 *        @ORM\Index(name="ix_fee_manual_alteration_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_fee_manual_alteration_alteration_type", columns={"alteration_type"}),
 *        @ORM\Index(name="ix_fee_manual_alteration_post_fee_status", columns={"post_fee_status"}),
 *        @ORM\Index(name="ix_fee_manual_alteration_pre_fee_status", columns={"pre_fee_status"}),
 *        @ORM\Index(name="ix_fee_manual_alteration_user_id", columns={"user_id"})
 *    }
 * )
 */
class FeeManualAlteration extends AbstractFeeManualAlteration
{

}
