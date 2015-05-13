<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;

/**
 * SiPenalty Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="si_penalty",
 *    indexes={
 *        @ORM\Index(name="ix_si_penalty_serious_infringement_id", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="ix_si_penalty_si_penalty_type_id", columns={"si_penalty_type_id"}),
 *        @ORM\Index(name="ix_si_penalty_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_si_penalty_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenalty extends AbstractSiPenalty
{

}
