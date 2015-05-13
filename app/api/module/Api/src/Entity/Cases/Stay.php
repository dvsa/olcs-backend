<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stay Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="stay",
 *    indexes={
 *        @ORM\Index(name="ix_stay_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_stay_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_stay_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_stay_outcome", columns={"outcome"}),
 *        @ORM\Index(name="ix_stay_stay_type", columns={"stay_type"})
 *    }
 * )
 */
class Stay extends AbstractStay
{

}
