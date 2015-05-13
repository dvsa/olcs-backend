<?php

namespace Dvsa\Olcs\Api\Entity\Opposition;

use Doctrine\ORM\Mapping as ORM;

/**
 * Opposition Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="opposition",
 *    indexes={
 *        @ORM\Index(name="ix_opposition_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_opposition_opposer_id", columns={"opposer_id"}),
 *        @ORM\Index(name="ix_opposition_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_opposition_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_opposition_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_opposition_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_opposition_opposition_type", columns={"opposition_type"}),
 *        @ORM\Index(name="ix_opposition_is_valid", columns={"is_valid"}),
 *        @ORM\Index(name="ix_opposition_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="ux_olbs_key", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Opposition extends AbstractOpposition
{

}
