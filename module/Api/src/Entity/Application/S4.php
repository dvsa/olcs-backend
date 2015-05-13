<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;

/**
 * S4 Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="s4",
 *    indexes={
 *        @ORM\Index(name="ix_s4_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_s4_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_s4_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_s4_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class S4 extends AbstractS4
{

}
